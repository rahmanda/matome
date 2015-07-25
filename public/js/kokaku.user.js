/**
 * Module: User
 * Description: manage user data
 * Dependencies:
 *   local:
 *     - [local module]
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */
function User () {

  var self = this;

  self.flash = new Flash();

  self.userKey = 'usrmm';

}

User.prototype.init = function() {
  // this.getUserData();
}

/**
 * Save user data
 * @return {void}
 */
User.prototype.fill = function(token) {
  var self = this;

  var data = self.getFromStorage();

  if(data != null) {
    self.render(data);
  } else {
    $.ajax({
      url: "http://localhost:8001/api/users",
      method: "GET",
      contentType: "application/json",
      beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + token);
      },
      success: function(response) {
        self.saveToStorage(response.user);
        self.render(response.user);
      },
      error: function(e, data) {
        console.log(e);
      }
    });
  }

}

User.prototype.render = function(data) {
  if(data) {
    $(".modal-setting .input-username input").val(data.username);
    $(".modal-setting .input-email input").val(data.email);
  }
}

User.prototype.getInput = function() {
  return {
    username : $(".modal-setting .input-username input").val(),
    email : $(".modal-setting .input-email input").val()
  };
}

User.prototype.update = function(token) {
  var self = this;
  var input = self.getInput();
  var user = self.getFromStorage();

  $.ajax({
    url: "http://localhost:8001/api/users/" + user.id,
    method: "PUT",
    contentType: "application/json",
    data: JSON.stringify(input),
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
      input.id = user.id;
      self.saveToStorage(input);
      self.flash.show("success");
    },
    error: function(e, data) {
      console.log(e);
    }
  }); 

}

/**
 * Get creator data
 * @return {object}
 */
User.prototype.getFromStorage = function() {
  var user =  localStorage.getItem(this.userKey);
  return JSON.parse(user)
}

User.prototype.saveToStorage = function(data) {
  localStorage.setItem(this.userKey, JSON.stringify(data));
}

User.prototype.removeFromStorage = function() {
  localStorage.removeItem(this.userKey);
}