/**
 * Module: Token
 * Description: handling token
 * Dependencies:
 *   local:
 *     - [local module]
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */
function Token () {
  var self = this;

  self.key = 'tknmm';
}

/**
 * Refresh token
 * @param  {string} token
 * @return {void}      
 */
Token.prototype.refresh = function(token) {
  var self = this;

  var data = {
    token: token
  }

  $.ajax({
    url: "http://localhost:8001/api/refresh",
    method: "POST",
    data: JSON.stringify(data),
    success: function(response) {
      self.saveToken(response.token);
    },
    error: function(error) {
      console.log(error);
    }
  });
}

/**
 * [get description]
 * @return {[type]} [description]
 */
Token.prototype.get = function() {
  return localStorage.getItem(this.key);
}

Token.prototype.set = function(token) {
  localStorage.setItem(this.key, token);
}

Token.prototype.remove = function() {
  localStorage.removeItem(this.key);
}