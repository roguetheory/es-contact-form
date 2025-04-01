<script>

export default {
  data() {
    return {
      form: {
        name: '',
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        message: ''
      },
      status: {
        validating: false,
        loading: false,
        success: false,
        error: false,
        message: ''
      }
    }
  },
  computed: {
    nameFieldHasError() {
      return this.status.validating && !this.form.name
    },
    emailFieldHasError() {
      let emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
      return this.status.validating && !emailRegex.test(this.form.email)
    },
    phoneFieldHasError() {
      const usPhoneRegex = /^(?:\+1[-.\s]?)?(\()?(\d{3})(?:\))?[-.\s]?(\d{3})[-.\s]?(\d{4})$/
      return this.status.validating && !usPhoneRegex.test(this.form.phone);
    },
    messageFieldHasError() {
      return this.status.validating && !this.form.message
    }

  },
  methods: {
    validate() {
      this.status.validating = true;
      this.status.message = '';

      if(!this.nameFieldHasError && !this.emailFieldHasError && !this.phoneFieldHasError && !this.messageFieldHasError && !this.messageFieldHasError ) {
        this.status.validating = false;
        this.send()
      }
    },
    async send() {

      this.status.loading = true;
      this.status.success = false;
      this.status.error = false;

      try {
        const formData = new FormData();
        formData.append('action', 'es_contact_form_submit');
        formData.append('nonce', EsContactForm.nonce);
        formData.append('first_name', this.form.first_name);
        formData.append('last_name', this.form.last_name);
        formData.append('name', this.form.name);
        formData.append('email', this.form.email);
        formData.append('message', this.form.message);

        const response = await fetch(EsContactForm.ajaxurl, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          this.status.success = true;
          this.status.message = data.data;
          this.form.name = '';
          this.form.first_name = '';
          this.form.last_name = '';
          this.form.email = '';
          this.form.message = '';
        } else {
          this.status.error = true;
          this.status.message = data.data;
        }
      } catch (error) {
        this.status.error = true;
        this.status.message = 'An error occurred. Please try again.';
        console.error(error);
      } finally {
        this.status.loading = false;

      }
    }
  }
}

</script>

<template>
<form class="wpcf7-form">
    <div class="col-one-third">
      <input name="address" v-model="form.name" type="text" placeholder="Your Name" :class="{error: nameFieldHasError}">
      <input name="fist_name" v-model="form.first_name" type="text" placeholder="First Name" class="nd">
      <input name="last_name" v-model="form.last_name" type="text" placeholder="Last Name" class="nd">
      <label for="address">
        <span class="error" v-if="nameFieldHasError">Name is required.</span>
        <span v-else>&nbsp;</span>
      </label>
    </div>
    <div class="col-one-third margin-one-third">
      <input name="email" v-model="form.email" type="email" placeholder="Your Email" :class="{error: emailFieldHasError}">
      <label for="email">
        <span class="error" v-if="emailFieldHasError">Valid email address required.</span>
        <span v-else>&nbsp;</span>
      </label>
    </div>
    <div class="col-one-third">
      <input name="phone" v-model="form.phone" type="text" placeholder="Your Phone" :class="{error: phoneFieldHasError}">
      <label for="phone">
        <span class="error" v-if="phoneFieldHasError">US phone number required.</span>
        <span v-else>&nbsp;</span>
      </label>
    </div>
    <div class="col-full">
      <textarea name="message" v-model="form.message" placeholder="Your Message" :class="{error: messageFieldHasError}"></textarea>
      <label for="message">
        <span class="error" v-if="messageFieldHasError">Message required.</span>
        <span v-else>&nbsp;</span>
      </label>
    </div>
    <div class="col-full"><p :class="{error: status.error, success: !status.error}">{{status.message}}</p></div>
    <div class="clearfix"></div>

    <div class="text-center">
      <div class="divider-single"></div>
      <button @click.prevent="validate" class="btn btn-primary btn-big" :disabled="status.loading">Send Email</button>
    </div>
  </form>
</template>

<style scoped>
.wpcf7-form input[type=text].nd {
    display: none;
  }

.wpcf7-form input[type=text].error, .wpcf7-form input[type=email].error, .wpcf7-form input[type=text].error:focus,
.wpcf7-form input[type=email].error, .wpcf7-form textarea.error, .wpcf7-form textarea.error:focus {
  border-color: red;
}

.wpcf7-form label span.error, .wpcf7-form p.error {
  color: red;
  font-weight: 700;
}

.wpcf7-form p.success {
  color: green;
  font-weight: 700;
}

</style>