/**
 * Module: Modal
 * Description: event handler for modal component
 * Dependencies:
 *   local:
 *     - Auth
 *     - Creator
 *   external:
 *     - jQuery
 *     - [external module - like open source]
 */

function Modal() {
  // current scope
  var self = this;

  // jQuery DOM
  self.modal = {
    generic: $(".modal"),
    tutorial: $(".modal-tutorial"),
    setting: $(".modal-setting")
  };

  self.tab = {
    button: $(".modal-setting .tab"),
    content: $(".modal-setting .group-setting")
  }

  self.setting = {
    sync: {
      content: $(".modal-setting .group-1"),
      button: $(".modal-setting .tab-sync")
    },
    creator: {
      content: $(".modal-setting .group-2"),
      button: $(".modal-setting .tab-creator")
    },
    account: {
      content: $(".modal-setting .group-3"),
      button: $(".modal-setting .tab-account")
    }
  }

  self.buttonClose = $(".act-modal .btn-close");
  self.buttonSaveCreator = $(".act-modal .btn-save-creator");
  self.buttonSaveAccount = $(".act-modal .btn-save-account");
  self.buttonSaveSync = $(".act-modal .btn-save-sync");

  self.creator = new Creator();
  self.user = new User();
  self.auth = new Auth();
  self.sync = new Sync();

  // event handler
  self.buttonClose.on("click", function(e) {
    self.hide(self.modal.generic);
  });

  self.tab.button.on("click", function(e) {
    e.preventDefault();

    self.switchTab($(this), self);
  });

  self.buttonSaveCreator.on("click", function(e) {
    e.preventDefault();

    self.creator.update(self.auth.getUser(), self.auth.getToken());
  });

  self.buttonSaveAccount.on("click", function(e) {
    e.preventDefault();

    self.user.update(self.auth.getToken());
  });

  self.buttonSaveSync.on("click", function(e) {
    e.preventDefault();

    self.sync.save(self.auth.getToken());
  });

  self.init();


}

Modal.prototype.init = function() {
  if(this.creator.getFromStorage() == null) {
    this.show($(".modal-setting"));
    this.setting.creator.button.trigger("click");
  } else {
    this.setting.sync.button.trigger("click");
  }
}

/**
 * Show component / trigger to add class 'show'
 * @param  {jQuery DOM} el
 * @return {void}   
 */
Modal.prototype.show = function(el) {
  el.addClass("show");
}

/**
 * Hide component / trigger to remove class 'show'
 * @param  {jQuery DOM} el
 * @return {void}   
 */
Modal.prototype.hide = function(el) {
  el.removeClass("show");
}

Modal.prototype.switchTab = function(target, parentScope) {
  var tab;

  if(target.hasClass("tab-sync")) {
    tab = parentScope.setting.sync;
    parentScope.sync.render(parentScope.auth.getToken());
  } else if(target.hasClass("tab-creator")) {
    tab = parentScope.setting.creator;
    parentScope.creator.fill(parentScope.auth.getUser(), parentScope.auth.getToken());
  } else {
    tab = parentScope.setting.account;
    parentScope.user.fill(parentScope.auth.getToken());
  }

  parentScope.resetTab();
  parentScope.showTab(tab);
}

Modal.prototype.resetTab = function() {
  this.tab.content.removeClass("show");
  this.tab.button.removeClass("active");
}

Modal.prototype.showTab = function(tab) {
  tab.content.addClass("show");
  tab.button.addClass("active");
}