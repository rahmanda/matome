/**
 * Module: Sync
 * Description: manage rsync preferences
 * Dependencies:
 *   local:
 *     - Flash
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */
function Sync() {
  var self = this;
  self.flash = new Flash();
  self.key = 'synmm'; 
}

Sync.prototype.render = function(token) { 
  var self = this;
  var data = self.getFromStorage();

  if(data) {
    self.fill(data);
  } else {
    $.ajax({
      url: "http://localhost:8001/api/scheduler/",
      method: "GET",
      beforeSend: function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + token);
      },
      success: function(response) {
        // console.log(response);
        self.fill(response.server);
        self.saveToStorage(response.server);
      },
      error: function(e, data) {
        console.log(e);
      }
    });
  }
}

Sync.prototype.fill = function(data) {
  if(data) {
    $(".modal-setting .input-id input").val(data.id);
    $(".modal-setting .input-schedule select").val(data.schedule);
    $(".modal-setting .input-hostname input").val(data.hostname);
    $(".modal-setting .input-address input").val(data.address);
    $(".modal-setting .input-directory input").val(data.directory);
  }
}

Sync.prototype.save = function(token) {
  var self = this;
  var input = self.getInput();
  if(input.id != '') {
    // console.log('update');
    self.update(input, token);
  } else {
    // console.log('create');
    self.create(input, token);
  }
}

Sync.prototype.create = function(input, token) {
  var self = this;
  delete input.id;

  $.ajax({
    url: "http://localhost:8001/api/scheduler/",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(input),
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
      // console.log(response);
      self.saveToStorage(input);
      self.flash.show("success");
    },
    error: function(e, data) {
      console.log(e);
    }
  });
}

Sync.prototype.update = function(input, token) {
  var self = this;
  var id = input.id;
  delete input.id;

  $.ajax({
    url: "http://localhost:8001/api/scheduler/"+id,
    method: "PUT",
    contentType: "application/json",
    data: JSON.stringify(input),
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
      // console.log(response);
      self.saveToStorage(response.server);
      self.flash.show("success");
    },
    error: function(e, data) {
      console.log(e);
    }
  });

}

Sync.prototype.getInput = function() {
  return {
    id: $(".modal-setting .input-id input").val(),
    schedule: $(".modal-setting .input-schedule select").val(),
    hostname: $(".modal-setting .input-hostname input").val(),
    address: $(".modal-setting .input-address input").val(),
    directory: $(".modal-setting .input-directory input").val()
  };
}

/**
 * Get creator data
 * @return {object}
 */
Sync.prototype.getFromStorage = function() {
  var sync =  localStorage.getItem(this.key);
  if(sync === 'undefined') {
    return null;
  } else {
    return JSON.parse(sync);
  }
}

Sync.prototype.saveToStorage = function(data) {
  localStorage.setItem(this.key, JSON.stringify(data));
}

Sync.prototype.removeFromStorage = function() {
  localStorage.removeItem(this.key);
}