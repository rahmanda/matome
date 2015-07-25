/**
 * Module: Auth
 * Description: authentication
 * Dependencies:
 *   local:
 *     - Flash
 *   external:
 *     - jQuery
 *     - js.cookie
 */
function Auth() {
  // current scope
  var self = this;

  self.domain = "http://localhost:8001/"; 

  // properties 
  self.tokenKey = 'tknmm';
  self.userKey = 'usrmm';
  self.syncKey = 'synmm';
  self.creatorKey = 'crtmm';
  self.flash = new Flash();
  self.nanobar = new Nanobar({
    id: "nanobar"
  });

  // jQuery DOM
  self.registerButton = $("#btn-register");
  self.loginButton = $(".input-submit input");

  // event handler
  self.registerButton.on("click", function(e) {
    e.preventDefault();

    self.register();
  });

  self.loginButton.on("click", function(e) {
    e.preventDefault();

    self.login();
  });
}

/**
 * Login to system
 * @param  {string} username
 * @param  {string} password
 * @return {void}         
 */
Auth.prototype.login = function() {

  var self = this;

  var credentials = self.getLoginData();

  $.ajax({
    url: "http://localhost:8001/api/users/login",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(credentials),
    success: function(response) {
      self.nanobar.go(100);
      self.saveToken(response.token);
      self.saveUser(response.token);
    },
    progress: function() {
      self.nanobar.go(50);
    },
    error: function(jqXHR) {
      var message = jqXHR.responseJSON.message; 
      self.nanobar.go(100);
      self.flash.show("error", message);
    }
  });

}

/**
 * Redirect to desire option
 * @param  {string} opt
 * @return {void}    
 */
Auth.prototype.redirectTo = function(opt) {
  if(opt == "app") {
    window.location.href = this.domain + "metadata?view=incomplete&orderBy=created_at&order=desc&page=1";
  } else if(opt == "login") {
    window.location.href = this.domain + "login";
  }
}

/**
 * Attempt register
 * @return {void}
 */
Auth.prototype.register = function() {
  var self = this;

  var credentials = self.getRegisterData();

  $.ajax({
    url: "http://localhost:8001/api/users",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(credentials),
    success: function(response) {
      self.nanobar.go(100);
      console.log(response);
      self.flash.show("success", "You have successfully been registered. Please <a href='http://localhost:8001/login'>login</a> to continue.");
    },
    progress: function() {
      self.nanobar.go(50);
    },
    error: function(jqXHR) {
      var message = jqXHR.responseJSON.message; 
      self.nanobar.go(100);
      self.flash.show("error", message.join(" "));
    }
  });

}

/**
 * Get login data form
 * @return {object}
 */
Auth.prototype.getLoginData = function() {
  return {
    username: $(".input-username input").val(),
    password: $(".input-password input").val()
  }
}

Auth.prototype.getRegisterData = function() {
  return {
    email: $("#email").val(),
    username: $("#username").val(),
    password: $("#password").val(),
    password_confirmation: $("#validate_password").val()
  }
}

/**
 * Logout user
 * @return {void}
 */
Auth.prototype.logout = function() {

  var self = this;

  var token = self.getToken();

  $.ajax({
    url: "http://localhost:8001/api/users/logout",
    method: "GET",
    contentType: "application/json",
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
      self.nanobar.go(100);
      localStorage.removeItem(self.tokenKey);
      localStorage.removeItem(self.userKey);
      localStorage.removeItem(self.syncKey);
      localStorage.removeItem(self.creatorKey);
      self.redirectTo("login");
    },
    progress: function() {
      self.nanobar.go(50);
    },
    error: function(jqXHR) {
      var message = jqXHR.responseJSON.message; 
      self.nanobar.go(100);
      self.flash.show("error", message.join(" "));
    }
  });

}

/**
 * Save token
 * @param  {string} token
 * @return {void}      
 */
Auth.prototype.saveToken = function(token) {

  // Cookies.set(this.tokenKey, token, { path: '/', expires: 7 });
  localStorage.setItem(this.tokenKey, token);

}

/**
 * Get token
 * @return {v} [description]
 */
Auth.prototype.getToken = function() {
  
  return localStorage.getItem(this.tokenKey);

}

/**
 * Check if user is authenticate
 * @return {Boolean}
 */
Auth.prototype.isAuthenticate = function() {

  if(this.getToken() != null) {
    return true;
  } else {
    return false;
  }

}

/**
 * Get user data
 * @return {object}
 */
Auth.prototype.getUser = function() {
  var user = localStorage.getItem(this.userKey);

  return JSON.parse(user);
}

/**
 * Save user data
 * @return {void}
 */
Auth.prototype.saveUser = function(token) {
  var self = this;

  $.ajax({
    url: "http://localhost:8001/api/users",
    method: "GET",
    contentType: "application/json",
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    progress: function() {
      self.nanobar.go(50);
    },
    success: function(response) {
      self.nanobar.go(100);
      localStorage.setItem(self.userKey, JSON.stringify(response.user));
      self.redirectTo("app");
    },
    error: function(e, data) {
      self.nanobar.go(100);
      console.log(e);
    }
  });

}