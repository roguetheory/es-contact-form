<?php

namespace ethersense\EsContactForm;

class EsContactForm
{
	const VERSION = '1.0.0';
    const HANDLE = 'es-contact-form';
    const JS_OBJECT_NAME = 'EsContactForm';
    const ADMIN_URL = 'admin-ajax.php';
    const NONCE_NAME = 'es_contact_form_nonce';


    public function __construct () {
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_ajax_es_contact_form_submit', array($this, 'handleRequest'));
        add_action('wp_ajax_nopriv_es_contact_form_submit', array($this, 'handleRequest'));
    }

    public function enqueueScripts() {
	    wp_enqueue_script(
		    self::HANDLE,
		    ES_CONTACT_FORM_PLUGIN_URL . 'dist/js/main.js',
		    array(),
		    self::VERSION,
		    true
	    );
        wp_enqueue_style(
            self::HANDLE,
            ES_CONTACT_FORM_PLUGIN_URL . 'dist/assets/main.css',
            array(),
            self::VERSION
        );
        wp_localize_script(
            self::HANDLE,
            self::JS_OBJECT_NAME,
            array(
                'ajaxurl' => admin_url(self::ADMIN_URL),
                'nonce' => wp_create_nonce(self::NONCE_NAME)
            )
        );
    }

    public function handleRequest () {
        if ($this->is_suspicious_request($_POST)) {
            $this->send_to_honeypot();
        }

        // Get and sanitize data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        // Validate data
        if (empty($name) || empty($email) || empty($phone) || empty($message)) {
            wp_send_json_error('Please fill in all fields');
        }

        if (!is_email($email)) {
            wp_send_json_error('Please enter a valid email address');
        }

        // Process the data (example: send email)
        $to = get_option('admin_email');
        $subject = 'New Contact Form Submission';
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $email_content = sprintf(
            'Name: %s<br>Email: %s<br>Phone: %s<br> Message: %s',
            $name,
            $email,
            $phone,
            nl2br($message)
        );

        $sent = wp_mail($to, $subject, $email_content, $headers);

        if ($sent) {
            wp_send_json_success('Your message has been sent.  We will get back to you shortly!');
        } else {
            wp_send_json_error('An error occurred while sending your message');
        }
    }

    public function is_suspicious_request( array $post) : bool {
        if (!isset($_POST['nonce']))  {
            return true;
        }
        if (!wp_verify_nonce($_POST['nonce'], self::NONCE_NAME)) {
            return true;
        }
        if(!empty($_POST['first_name'])) {
            return true;
        }
        if(!empty($_POST['last_name'])) {
            return true;
        }
        return false;
    }

    public function send_to_honeypot()
    {
        wp_send_json_success('Message sent successfully');
    }
}