/**
 * Module: Statistic
 * Description: Metadata statistic, displayed in sideNav
 * Dependencies:
 *   local:
 *     - [local module]
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */
function Statistic() {
  this.domain = "http://localhost:8001/";
}

/**
 * Get statistic data
 * @return {void}
 */
Statistic.prototype.getStat = function () {
  var self = this;
  $.ajax({
    url: this.domain + "metadata/count",
    method: "GET",
    dataType: "json",
    success: function(data) {
      self.drawStatistic(data);
    }
  });
}

/**
 * Draw statistic in sideNav
 * @param  {object} data
 * @return {void}
 */
Statistic.prototype.drawStatistic = function (data) {
  var sideNav = $(".side-nav");
  var uncompleteNav = sideNav.find("ul .go-incomplete");
  var completeNav = sideNav.find("ul .go-complete");
  var allNav = sideNav.find("ul .go-all");

  uncompleteNav.find("span").text(data.uncomplete);
  completeNav.find("span").text(data.complete);
  allNav.find("span").text(data.all);
}

