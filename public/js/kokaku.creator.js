/**
 * Module: Creator
 * Description: manage creator data
 * Dependencies:
 *   local:
 *     - Flash
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */
function Creator () { 

  var self = this; 

  self.flash = new Flash();

  self.creatorKey = 'crtmm';

}

Creator.prototype.init = function() {
 
}

/**
 * Save creator data
 * @return {void}
 */
Creator.prototype.fill = function(user, token) {
  var self = this;

  // var token = self.auth.getToken();

  // var user = self.auth.getUser();

  var data = self.getFromStorage();

  if(data != null) {
    self.render(data);
  } else {
    $.ajax({
      url: "http://localhost:8001/api/creators",
      method: "GET",
      contentType: "application/json",
      beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + token);
      },
      success: function(response) {
        self.saveToStorage(response.creator);
        self.render(response.creator);
      },
      error: function(e, data) {
        console.log(e);
      }
    });
  }

}

Creator.prototype.render = function(data) {
  if(data) {
    $(".modal-setting .input-id input").val(data.id);
    $(".modal-setting .input-name input").val(data.name);
    $(".modal-setting .input-type input").val(data.type);
    $(".modal-setting .input-administrativeLevel select").val(data.administrativeLevel);
    $(".modal-setting .input-region").val(data.region);
    $(".modal-setting .input-fields input").val(data.fields);
    $(".modal-setting .input-siteUrl input").val(data.siteUrl);
  }
}

Creator.prototype.getInput = function(user) {
  return {
    id : $(".modal-setting .input-id input").val(),
    userId : user.id,
    name : $(".modal-setting .input-name input").val(),
    type : $(".modal-setting .input-type input").val(),
    administrativeLevel : $(".modal-setting .input-administrativeLevel select").val(),
    region : $(".modal-setting .input-region").val(),
    fields : $(".modal-setting .input-fields input").val(),
    siteUrl : $(".modal-setting .input-siteUrl input").val()
  };
}

Creator.prototype.update = function(user, token) {
  var self = this;
  var input = self.getInput(user);

  $.ajax({
    url: "http://localhost:8001/api/creators/",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(input),
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
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
Creator.prototype.getFromStorage = function() {
  var creator = localStorage.getItem(this.creatorKey);

  if(creator === 'undefined') {
    return null;
  } else {
    return JSON.parse(creator); 
  }
}

Creator.prototype.saveToStorage = function(data) {
  localStorage.setItem(this.creatorKey, JSON.stringify(data));
}

Creator.prototype.removeFromStorage = function() {
  localStorage.removeItem(this.creatorKey);
}