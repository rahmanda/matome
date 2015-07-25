/**
 * Module: Metadatas
 * Description: Generate metadata DOM
 * Dependencies:
 *   local:
 *     - [local module]
 *   external:
 *     - jQuery
 *     - jquery.geturlparam.js
 */

function Metadatas() {
  this.domain = "http://localhost:8001/";
  // properties
  this.urls = {
    all: this.domain + "metadata/fetchAll/",
    complete: this.domain + "metadata/fetchComplete/",
    incomplete: this.domain + "metadata/fetchIncomplete/"
  };

  this.pagination = new Pagination();
  
  this.urlParam = {
    orderBy: $(document).getUrlParam('orderBy'),
    order: $(document).getUrlParam('order'),
    page: $(document).getUrlParam('page'),
    view: $(document).getUrlParam('view')
  };

  this.auth = new Auth();

  // jQuery DOM
  this.lists = $(".list-items .lists");
  this.template = $("#template-metadatas");

  // initial function
  this.init();
}

/**
 * Initial function
 * @return {void}
 */
Metadatas.prototype.init = function() {

  this.setUrlParam(this.urlParam.orderBy, this.urlParam.order, this.urlParam.page);
  this.draw(this.urlParam.view);

}

/**
 * Set url param
 * @param {string} orderBy
 * @param {string} order  
 * @param {string} page   
 */
Metadatas.prototype.setUrlParam = function(orderBy, order, page) {

  this.urls.all = this.urls.all + orderBy + "/" + order + "?page=" + page;
  this.urls.complete = this.urls.complete + orderBy + "/" + order + "?page=" + page;
  this.urls.incomplete = this.urls.incomplete + orderBy + "/" + order + "?page=" + page;

  // console.log(this.urls);

}

/**
 * Draw metadata list DOM
 * @param  {string} option
 * @return {void}       
 */
Metadatas.prototype.draw = function(option) {

  var self = this;
  var option = option || "all"; 

  $.ajax({
    url: self.urls[option],
    method: "GET",
    dataType: "json",
    success: function(response) {
      self.generateDOM(response.data, option, self);
      self.pagination.draw(response);
    },
    error: function(e, data) {
      console.log(e);
    }
  });

}

/**
 * Generate DOM for metadata
 * @param  {object} data       
 * @param  {string} option     
 * @param  {scope} parentScope
 * @return {void}            
 */
Metadatas.prototype.generateDOM = function(data, option, parentScope) {

  var template = parentScope.template.html();
  Mustache.parse(template);
  var input = {  
    meta: data,
    formatDate: function() { return function(rawdata, render) { return moment(render(rawdata)).fromNow();  }; }
  };
  parentScope.lists.html(Mustache.render(template, input));

}

/**
 * Date format for mustachejs template
 * @param  {string} date
 * @return {string}        
 */
Metadatas.prototype.mustacheFormatDate = function(date) {
  return moment(date, "YYYYMMDD").fromNow();
}

Metadatas.prototype.hasCreator = function() {
  var self = this;

  var token = self.auth.getToken();

  $.ajax({
    url: "http://localhost:8001/api/test",
    method: "GET",
    contentType: "application/json",
    beforeSend: function(xhr) {
      xhr.setRequestHeader("Authorization", "Bearer " + token);
    },
    success: function(response) {
      $("p.user-name").text(response.user.username);
    },
    error: function(e, data) {
      console.log(e);
    }
  });
}