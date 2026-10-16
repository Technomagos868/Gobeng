const Gemstone = {
  custom_url: CLIENT_CUSTOM_URL,
  //custom_url : "https://coralisstudio.com/",
  base_url: CLIENT_BASE_URL,
  //base_url : "https://coralisstudio.com/gemstone/",
  primeContainer: $("#gs-content"),
  messageTimeoutDelay: 3000,
  qsDelay: 500,
  navigateDelay: 300,
  qstimer: null,
  messageTimer: null,
  navigateTimer: null,
  extendSessionInterval: null,
  extendSessionDelay: 300000,
  buttonList: [],
  is_maxed: false,
  allowNavigate: true,
  promptNavigate: false,
  extendSession: false,
  currentFormPage: 1,
  checkImageUrl: "https://coralisstudio.com/check.png",
  checkImageSize: 11461,
  checkRequestCount: 5,
  checkRequestInterval: 10,
  firebaseToken: null,
  dismissBrowserWarning: function () {
    document.getElementById("browserWarningOverlay").remove();
  },
  navigate: function (targetPage, event = false) {
    if (event) {
      event.stopPropagation();
    }
    if (Gemstone.allowNavigate === true) {
      Gemstone.preventDoubleNavigate();
      if (Gemstone.promptNavigate === true) {
        const actions = [
          {
            type: "warning",
            icon: "delete",
            action: 'gsNavigateForced("' + targetPage + '")',
            label: "Discard Changes and Continue",
          },
        ];
        Gemstone.prompt(
          "There are unsaved changes on current page. If you switch page now, those changes might be lost. Continue switching page?",
          "Unsaved Changes",
          actions
        );
      } else {
        let controller = Gemstone.base_url;
        if (targetPage.includes("/")) {
          controller = Gemstone.custom_url;
        }
        const targetURL = controller + targetPage;
        if (
          location.hostname === "localhost" ||
          location.hostname === "127.0.0.1" ||
          window.navigator.onLine === true
        ) {
          Gemstone.showLoader();
          if (targetPage && targetPage !== "") {
            $.ajax({
              url: targetURL,
              type: "POST",
              data: {
                ajax: true,
                tabToken,
              },
              success: function (result) {
                Gemstone.onNavigateSuccess(result, targetPage);
              },
              error: function () {
                Gemstone.navigate("view_404");
                //Error here
              },
            });
          }
        } else {
          Gemstone.prompt(
            "Your device lost its internet connection, Please re-connect to the internet to continue.",
            "No Internet Connection",
            false
          );
        }
      }
    }
  },
  getUniqueId: function (prefix = "") {
    const d = new Date();
    const row_id =
      prefix +
      d.getHours() +
      d.getMinutes() +
      d.getSeconds() +
      d.getMilliseconds() +
      Math.floor(Math.random() * 1000).toString();
    return row_id;
  },
  back: function () {
    getLastPage();
  },
  submitForm: function (formID, targetPage, target = false) {
    let isValid = true;
    let errorMessage = "";
    let identicValue = "";
    $(".gs-form-warning").remove();
    $("#" + formID + " .required").each(function () {
      if (!this.value) {
        errorMessage = "Marked fields cannot be empty.";
        $(this).addClass("warning");
        isValid = false;
      }
    });
    $("#" + formID + " .alphanumeric").each(function () {
      const allowed_value = /^[a-zA-Z0-9]+$/;
      if (!allowed_value.test(this.value)) {
        errorMessage += " Marked fields can only contain letter and number.";
        $(this).addClass("warning");
        isValid = false;
      }
    });
    $("#" + formID + " .identic").each(function () {
      if (identicValue != "") {
        if (this.value != identicValue) {
          errorMessage += " Marked fields must contain the same value.";
          $(this).addClass("warning");
          isValid = false;
        }
      }
      identicValue = this.value;
    });
    if (!isValid) {
      $("#" + formID).prepend(
        "<div class='gs-form-warning'>" + errorMessage + "</div>"
      );
    } else {
      let controller = Gemstone.base_url;
      if (targetPage.includes("/")) {
        controller = Gemstone.custom_url;
      }
      const targetURL = controller + targetPage;
      const form = document.getElementById(formID);
      if (target == false) {
        let input = new FormData(form);
        input.append("ajax", "true");
        input.append("target", target);
        input.append("tabToken", tabToken);
        Gemstone.showLoader();
        if (targetPage != "") {
          $.ajax({
            url: targetURL,
            type: "POST",
            data: input,
            processData: false,
            contentType: false,
            cache: false,
            success: function (result) {
              result.targetPage = targetPage;
              Gemstone.onNavigateSuccess(result, targetPage);
            },
            error: function () {
              Gemstone.navigate("view_404");
            },
          });
        }
      } else if (target == "_blank") {
        form.setAttribute("action", targetURL);
        form.setAttribute("method", "post");
        form.setAttribute("target", "_blank");
        form.submit();
      }
    }
  },
  preventDoubleNavigate: function () {
    Gemstone.allowNavigate = false;
    Gemstone.navigateTimer = setTimeout(function () {
      Gemstone.allowNavigate = true;
    }, Gemstone.navigateDelay);
  },
  prompt: function (message, title, actions = false) {
    let html = "";
    if (title === "" || title === null) {
      title = "Message";
      title = title.charAt(0).toUpperCase() + title.slice(1);
    }
    html += "<div class='gs-popup-overlay' id='alertPop'>";
    html += "<div class='gs-popup-container'>";
    html += "<div class='gs-popup-title'>" + title + "</div>";
    html += "<div class='gs-popup-message'>" + message + "</div>";
    html += "<div class='gs-popup-button-container'>";
    if (actions !== false && Array.isArray(actions)) {
      for (const element of actions) {
        if (
          element.label !== undefined &&
          (typeof element.action !== "undefined" ||
            typeof element.link !== "undefined")
        ) {
          if (typeof element.type === "undefined") {
            element.type = "primary";
          }
          if (typeof element.icon === "undefined") {
            element.icon = "check";
          }
          if (typeof element.action !== "undefined") {
            html +=
              "<div class='gs-popup-button " +
              element.type +
              "' onclick='" +
              element.action +
              "'><i class='gs-icon " +
              element.icon +
              "'></i><p>" +
              element.label +
              "</p></div>";
          } else {
            html +=
              "<div class='gs-popup-button " +
              element.type +
              "' onclick='gsNavigate(\"" +
              element.link +
              "\")'><i class='gs-icon " +
              element.icon +
              "'></i><p>" +
              element.label +
              "</p></div>";
          }
        }
      }
    }
    html +=
      "<div class='gs-popup-button'><i class='gs-icon close'></i><p>Close</p></div>";
    html += "</div>";
    html += "</div>";
    html += "</div>";
    $("body").append(html);
    $(".gs-popup-button").on("click", function () {
      Gemstone.closeAlert();
    });
    Gemstone.bindListeners();
  },
  closeAlert: function () {
    $("#alertPop").remove();
  },
  bindListeners: function (scrollToTop = false) {
    $("input, textarea, select").on("change", function () {
      if (this.classList.contains("no-prompt")) {
        //do not prompt or extend session
      } else {
        Gemstone.promptNavigate = true;
        if (Gemstone.extendSession === false) {
          Gemstone.extendSession = true;
          Gemstone.extendSessionInterval = window.setInterval(function () {
            Gemstone.requestExtendSession();
          }, Gemstone.extendSessionDelay);
        }
      }
    });
    $("input").on("focus", function () {
      $(this).removeClass("warning");
    });
    $("input").on("blur", function () {
      window.setTimeout(function () {
        document.getElementsByClassName("gs-qs-container").innerHTML = "";
      }, 300);
    });
    $(".gs-file-input").on("change", function (e) {
      Gemstone.fileInputUX(e.target);
    });
    $(".gs-dropdown-trigger").on("click", Gemstone.toggleDropdown);
    $("textarea").on("focus", function () {
      $(this).removeClass("warning");
    });
    const numberInput = $(".number-format");
    for (const element of numberInput) {
      element.value = Gemstone.numberFormat(element.value);
    }
    numberInput.on("keyup", function (e) {
      if (e.keyCode !== 8 && e.keyCode !== 46) {
        this.value = Gemstone.numberFormat(this.value);
      }
    });
    const enterOverride = $(".enter-override");
    enterOverride.on("keyup", function (e) {
      if (e.keyCode == 13) {
        const enterAction = this.getAttribute("enter-action");
        window[enterAction]();
      }
    });
    if (scrollToTop === true) {
      window.setTimeout(function () {
        $("body, html, #gs-content").animate({ scrollTop: 0 }, "fast");
      }, 10);
    }
    const buttons = document.querySelectorAll(
      ".gs-button, .gs-floating-button, .gs-buttonbar-button, .gs-popup-button, .gs-table-button"
    );
    if (buttons) {
      for (const element of buttons) {
        element.setAttribute("tabindex", "0");
        element.removeEventListener("keyup", gsClickButton);
        element.addEventListener("keyup", gsClickButton);
      }
    }
    $(".gs-tab-nav-button").on("click", gsToggleTab);
  },
  onNavigateSuccess: function (result, targetPage) {
    Gemstone.closeAlert();
    Gemstone.primeContainer.html(result);
    Gemstone.primeContainer.removeClass("hidden");
    Gemstone.primeContainer.find(".gs-load-overlay").remove();
    Gemstone.messageTimeout();
    Gemstone.carouselInit();
    //Gemstone.captionInit();//Check if this code is still needed or not
    //gsChartInit();//Deprecated, replace with chart js library
    const index = targetPage.indexOf("?");
    if (index > -1) {
      targetPage = targetPage.substring(0, index);
    }
    for (const element of Gemstone.buttonList) {
      if (element.getAttribute("data-target") === targetPage) {
        element.classList.add("active");
      } else {
        element.classList.remove("active");
      }
    }
    Gemstone.showTab();
    Gemstone.bindListeners();
    Gemstone.measureConnectionSpeed();
    Gemstone.promptNavigate = false;
    Gemstone.extendSession = false;
    Gemstone.currentFormPage = 1;
    window.clearInterval(Gemstone.extendSessionInterval);

    //FALLBACK SUPPORT FOR OLDER GEMSTONE MIGRATION
    promptNavigate = false;
    extendSession = false;
    currentFormPage = 1;
    window.clearInterval(extendSessionInterval);
  },
  showLoader: function () {
    if (Gemstone.is_maxed == true) {
      Gemstone.primeContainer.append(
        "<div class='gs-load-overlay maximized'></div>"
      );
    } else {
      Gemstone.primeContainer.append("<div class='gs-load-overlay'></div>");
    }
  },
  hideLoader: function () {
    Gemstone.primeContainer.find(".gs-load-overlay").remove();
  },
  fileInputUX: function (target) {
    const label = target.nextElementSibling;
    const labelVal = label.innerHTML;
    let fileName = "";
    if (target.files.length > 0) {
      if (target.files.length > 1) {
        fileName = target.files.length + " files selected";
      } else {
        fileName = target.files[0].name;
      }
    }
    if (fileName !== "") {
      label.innerHTML = fileName;
    } else {
      label.innerHTML = labelVal;
    }
  },
  toggleDropdown: function () {
    this.nextSibling.nextSibling.classList.toggle("expanded");
  },
  numberFormat: function (value) {
    let negative = "";
    if (value.toString()[0] === "-") {
      negative = "-";
    }
    const raw = value
      .toString()
      .replace(/(?!\.)\D/g, "")
      .split(".");
    const whole = raw[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    let decimal = false;
    if (raw.length > 1) {
      decimal = raw[1];
    }
    if (decimal !== false && (decimal == "" || parseInt(decimal) > 0)) {
      return negative + whole + "." + decimal;
    } else {
      return negative + whole;
    }
  },
  messageTimeout: function () {
    window.clearTimeout(Gemstone.messageTimer);
    Gemstone.messageTimer = setTimeout(function () {
      $(".gs-message").addClass("fade");
    }, Gemstone.messageTimeoutDelay);
  },
  carouselInit: function () {
    const target = document.getElementsByClassName("gs-carousel");
    for (const element of target) {
      gsCarousel(element, false, 400);
    }
  },
  captionInit: function () {
    const target = document.getElementsByClassName("gs-caption");
    for (const element of target) {
      element.addEventListener("mouseover", Gemstone.alignCaption);
    }
  },
  alignCaption: function () {
    if (window.innerWidth - event.clientX <= 200) {
      this.classList.add("gs-caption-right");
    } else {
      this.classList.remove("gs-caption-right");
    }
  },
  showTab: function () {
    const tab_helper = document.getElementsByClassName("gs-tab-helper");
    if (tab_helper.length > 0) {
      const active_index = tab_helper[0].getAttribute("active-tab");
      const nav_list = document.getElementsByClassName("gs-tab-nav-button");
      const page_list = document.getElementsByClassName("gs-tab-page");
      for (let i = 0; i < nav_list.length; i++) {
        if (i === active_index - 1) {
          nav_list[i].classList.add("active");
          if (!nav_list[i].classList.contains("dummy")) {
            document
              .getElementById(nav_list[i].getAttribute("tab-id"))
              .classList.add("active");
            page_list[i].classList.add("active");
          }
        } else {
          nav_list[i].classList.remove("active");
          if (!nav_list[i].classList.contains("dummy")) {
            document
              .getElementById(nav_list[i].getAttribute("tab-id"))
              .classList.remove("active");
          }
        }
      }
    }
  },
  measureConnectionSpeed: function () {
    if (Gemstone.checkRequestCount >= Gemstone.checkRequestInterval) {
      let startTime, endTime;
      const lagTimer = window.setTimeout(function () {
        document.getElementById("gsInetWarning").classList.remove("hidden");
      }, 1000);
      const download = new Image();
      download.onload = function () {
        window.clearTimeout(lagTimer);
        endTime = new Date().getTime();
        showResults();
      };
      download.onerror = function (err, msg) {
        console.log("error testing speed.");
      };
      startTime = new Date().getTime();
      download.src = Gemstone.checkImageUrl + "?acv=" + startTime;
      function showResults() {
        const duration = (endTime - startTime) / 1000;
        const bitsLoaded = Gemstone.checkImageSize * 8; //download size in bytes
        const speedBps = (bitsLoaded / duration).toFixed(2);
        const speedKbps = (speedBps / 1024).toFixed(2);
        const speedMbps = (speedKbps / 1024).toFixed(2);
        document.getElementById("gsInetSpeedLabel").innerHTML =
          " (" + numberFormat(speedMbps) + " Mbps)";
        if (speedMbps < 0.5) {
          document.getElementById("gsInetWarning").classList.remove("hidden");
        } else {
          document.getElementById("gsInetWarning").classList.add("hidden");
        }
      }
      Gemstone.checkRequestCount = 0;
    } else {
      Gemstone.checkRequestCount++;
    }
  },
  requestExtendSession: function () {
    let domForm = document.getElementsByClassName("gs-autosave");
    let savedData = false;
    let methodName = false;
    if (domForm != null) {
      let keys = [];
      let values = [];
      domForm.forEach((item) => {
        let formData = new FormData(item);
        for (let pair of formData.entries()) {
          keys.push(pair[0]);
          values.push(pair[1]);
        }
      });
      savedData = {};
      keys.forEach((key, i) => (savedData[key] = values[i]));
      savedData = JSON.stringify(savedData);

      let breadcrumbLink = document.querySelectorAll(".gs-breadcrumb-link");
      let rawLink =
        breadcrumbLink[breadcrumbLink.length - 1].getAttribute("onclick");
      rawLink += "?";
      methodName = rawLink.substring(
        rawLink.indexOf('"') + 1,
        rawLink.indexOf("?")
      );
    }
    $.ajax({
      url: base_url + "extend_session",
      type: "POST",
      data: {
        form: savedData,
        method: methodName,
        ajax: true,
        tabToken,
      },
      success: function (result) {
        console.log(result);
        console.log("Session extended while completing form.");
      },
      error: function () {
        console.log("Failed to extend session.");
      },
    });
  },
  getActiveTabIndex: function () {
    let tabList = document.getElementsByClassName("gs-tab-nav-button");
    let tab = 1;
    for (let i = 0; i < tabList.length; i++) {
      if (tabList[i].classList.contains("active")) {
        tab = i + 1;
      }
    }
    return tab;
  },
  init: function () {
    document.addEventListener("keydown", function (e) {
      if (e.keyCode == 13 && document.activeElement.type != "textarea") {
        e.preventDefault();
        const inputList = document.getElementsByTagName("input");
        for (const element of inputList) {
          if (element.type == "submit") {
            element.click();
          }
        }
      }
    });
    document.addEventListener("DOMContentLoaded", function () {
      Gemstone.buttonList =
        document.getElementsByClassName("gs-sidebar-button");
      //FALLBACK SUPPORT FOR OLDER GEMSTONE MIGRATION
      buttonList = document.getElementsByClassName("gs-sidebar-button");
    });
    window.onbeforeunload = function () {
      return "Leaving this page will close the application.";
    };
    $(".gs-sidebar-button").on("click", Gemstone.sidebarNavigate);
    $(".gs-sidebar-group-title").on("click", Gemstone.toggleSidebarGroup);
    $(".gs-sidebar-trigger").on("click", Gemstone.toggleSidebar);
    $("#desktopMenuTrigger").on("click", Gemstone.minimizeSidebar);
  },
  sidebarNavigate: function () {
    const targetLink = $(this).attr("data-target");
    $(".gs-sidebar-button").removeClass("active");
    $(this).addClass("active");
    Gemstone.closeSidebar();
    Gemstone.navigate(targetLink);
  },
  closeSidebar: function () {
    $(".gs-sidebar-wrapper").removeClass("expanded");
  },
  toggleSidebarGroup: function () {
    $(this).parent().toggleClass("expanded");
  },
  toggleSidebar: function () {
    $(".gs-sidebar-wrapper").toggleClass("expanded");
  },
  minimizeSidebar: function () {
    if (is_maxed == false) {
      is_maxed = true;
    } else {
      is_maxed = false;
    }
    $(".gs-sidebar-wrapper").toggleClass("minimized");
    $(".gs-prime-wrapper").toggleClass("maximized");
    $(".gs-sidebar-button-group").removeClass("expanded");
    window.dispatchEvent(new Event("resize"));
  },
  requestPushNotification: async function () {
    Gemstone.registerFirebaseToken();
    if (Notification.permission !== "granted") {
      await Notification.requestPermission();
    }
  },
  registerFirebaseToken: function () {
    $.ajax({
      url: Gemstone.base_url + "register_firebase_token",
      type: "POST",
      dataType: "json",
      data: {
        ajax: true,
        token: Gemstone.firebaseToken,
        tabToken,
      },
      success: function (result) {
        if (result.success == true) {
          Gemstone.navigate("reload_current_page");
        }
      },
      error: function (result) {
        console.log(result);
      },
    });
  },
  unregisterFirebaseToken: function () {
    $.ajax({
      url: Gemstone.base_url + "unregister_firebase_token",
      type: "POST",
      dataType: "json",
      data: {
        ajax: true,
        token: Gemstone.firebaseToken,
        tabToken,
      },
      success: function (result) {
        if (result.success == true) {
          Gemstone.navigate("reload_current_page");
        }
      },
      error: function (result) {
        console.log(result);
      },
    });
  },
  quickSearch: function (inputTextID, targetPage, containerID, keyboardEvent = false, callback = false) {
    var process_search = false;
    var container = $("#" + containerID);
    var focusedInput = document.getElementsByClassName("qs-focus");
    for (let i = 0; i < focusedInput.length; i++) {
      focusedInput[i].classList.remove("qs-focus");
    }
    container.addClass("qs-focus");
    var input = document.getElementById(inputTextID);
    const rect = input.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    if (rect.bottom > viewportHeight * 0.8) {
      container.addClass("qs-bottom");
    } else {
      container.removeClass("qs-bottom");
    }
    if (keyboardEvent) {
      if (keyboardEvent.keyCode >= 37 && keyboardEvent.keyCode <= 40) {
        //arrow navigation
        var items = container.find(".gs-qs-item");
        if (items.length > 0) {
          var max_index = items.length - 1;
          var previous = false;
          for (var i = 0; i <= max_index; i++) {
            if (items[i].classList.contains("selected")) {
              previous = true;
              selected_index = i;
              //console.log("selected on index: " + i);
              items[i].classList.remove("selected");
            }
          }
          //console.log("was on index: " + selected_index + "/" + max_index);
          if (previous == true) {
            if (keyboardEvent.keyCode == 40) {
              selected_index++;
              //console.log("Index Increased to: " + selected_index);
            } else if (keyboardEvent.keyCode == 38) {
              selected_index--;
              //console.log("Index Decreased to: " + selected_index);
            }
          } else {
            selected_index = 0;
          }
          if (selected_index < 0) {
            selected_index = 0;
          }

          if (selected_index > max_index) {
            selected_index = max_index;
          }
          items[selected_index].classList.add("selected");
        }
      } else if (keyboardEvent.keyCode == 13) {
        //enter hit
        var selected = container.find(".selected");
        if (selected.length > 0) {
          selected[0].click();
        }
      } else {
        process_search = true;
      }
    } else {
      process_search = true;
    }

    if (process_search == true) {
      //console.log("searching...");
      var keyword = $("#" + inputTextID).val();
      let controller = Gemstone.base_url;
      if (targetPage.includes("/")) {
        controller = Gemstone.custom_url;
      }
      const targetURL = controller + targetPage;
      window.clearTimeout(qstimer);
      qstimer = setTimeout(function () {
        $.ajax({
          url: targetURL,
          type: "POST",
          data: {
            keyword: keyword,
            ajax: true,
            tabToken,
          },
          success: function (data) {
            container.html(data);
            if (callback != false) {
              callback();
            }
          },
        });
      }, qsDelay);
    }
  },
  checkEmpty: function (inputTextID, containerID) {
    let input = document.getElementById(inputTextID);
    let container = document.getElementById(containerID);
    setTimeout(function () {
      container.innerHTML = "";
    }, 300);
  }
};
Gemstone.init();
//OLD CODE BELOW
var custom_url = CLIENT_CUSTOM_URL;
//var custom_url = "https://coralisstudio.com/";
var base_url = CLIENT_BASE_URL;
//var base_url = "https://coralisstudio.com/gemstone/";
const primeContainer = $("#gs-content");
const messageTimeout = 3000;
const qsDelay = 500;
const navigateDelay = 300;
var qstimer = null;
var messageTimer;
var navigateTimer;
var extendSessionInterval = null;
var buttonList;
var is_maxed = false;
var allowNavigate = true;
var promptNavigate = false;
var extendSession = false;
var currentFormPage = 1;
var checkImageUrl = "https://coralisstudio.com/check.png";
var checkImageSize = 11461;
var checkRequestCount = 5;
var checkRequestInterval = 10;
window.onresize = function () {
  gsCarouselResize();
};
function gsShowLoader() {
  if (is_maxed == true) {
    primeContainer.append("<div class='gs-load-overlay maximized'></div>");
  } else {
    primeContainer.append("<div class='gs-load-overlay'></div>");
  }
}

function gsHideLoader() {
  primeContainer.find(".gs-load-overlay").remove();
}

function preventDoubleNavigate() {
  allowNavigate = false;
  navigateTimer = setTimeout(function () {
    allowNavigate = true;
  }, navigateDelay);
}

function gsNavigateForced(targetPage) {
  if (allowNavigate === true) {
    preventDoubleNavigate();
    let controller = base_url;
    if (targetPage.includes("/")) {
      controller = custom_url;
    }
    const targetURL = controller + targetPage;
    gsShowLoader();
    if (targetPage != "") {
      $.ajax({
        url: targetURL,
        type: "POST",
        data: {
          ajax: true,
          tabToken,
        },
        success: function (result) {
          gsOnNavigateSuccess(result, targetPage);
        },
        error: function () {
          gsNavigate("view_404");
        },
      });
    }
  }
}

function gsNavigate(targetPage, requireConfirmation, confirmMessage) {
  if (allowNavigate === true) {
    preventDoubleNavigate();
    if (promptNavigate === true) {
      const actions = [
        {
          type: "warning",
          icon: "delete",
          action: 'gsNavigateForced("' + targetPage + '")',
          label: "Discard Changes and Continue",
        },
      ];
      gsPrompt(
        "There are unsaved changes on current page. If you switch page now, those changes might be lost. Continue switching page?",
        "Unsaved Changes",
        actions
      );
    } else {
      let confirmation = false;
      if (requireConfirmation == true) {
        if (confirmMessage == "" || confirmMessage == null) {
          confirmMessage = "Are you sure you want to perform this action?";
        }
        confirmation = confirm(confirmMessage);
      } else {
        confirmation = true;
      }
      if (confirmation == true) {
        let controller = base_url;
        if (targetPage.includes("/")) {
          controller = custom_url;
        }
        const targetURL = controller + targetPage;
        gsShowLoader();
        if (targetPage != "") {
          $.ajax({
            url: targetURL,
            type: "POST",
            data: {
              ajax: true,
              tabToken,
            },
            success: function (result) {
              gsOnNavigateSuccess(result, targetPage);
            },
            error: function () {
              gsNavigate("view_404");
            },
          });
        }
      }
    }
  }
}

function gsNavigateCustom(
  controller_name,
  targetPage,
  requireConfirmation,
  confirmMessage
) {
  if (allowNavigate === true) {
    preventDoubleNavigate();
    var confirmation = false;
    if (promptNavigate === true) {
      var actions = [
        {
          type: "warning",
          icon: "delete",
          action: 'gsNavigateForced("' + targetPage + '")',
          label: "Discard Changes and Continue",
        },
      ];
      gsPrompt(
        "There are unsaved changes on current page. If you move page now, those changes might be lost. Continue moving page?",
        "Unsaved Changes",
        actions
      );
    } else {
      if (requireConfirmation == true) {
        if (confirmMessage == "" || confirmMessage == null) {
          confirmMessage = "Are you sure you want to perform this action?";
        }
        confirmation = confirm(confirmMessage);
      } else {
        confirmation = true;
      }
      if (confirmation == true) {
        var targetURL = custom_url + controller_name + "/" + targetPage;
        gsShowLoader();
        if (targetPage != "") {
          $.ajax({
            url: targetURL,
            type: "POST",
            data: {
              ajax: true,
              tabToken,
            },
            success: function (result) {
              result.targetPage = targetPage;
              gsOnNavigateSuccess(result, targetPage);
            },
            error: function () {
              gsNavigate("view_404");
            },
          });
        }
      }
    }
  }
}

function gsBack() {
  if (promptNavigate === true) {
    var actions = [
      {
        type: "warning",
        icon: "delete",
        action: "getLastPage(false)",
        label: "Discard Changes and Continue",
      },
    ];
    gsPrompt(
      "There are unsaved changes on current page. If you move page now, those changes might be lost. Continue moving page?",
      "Unsaved Changes",
      actions
    );
  } else {
    gsShowLoader();
    $.ajax({
      url: base_url + "view_last_page",
      type: "POST",
      data: {
        ajax: true,
      },
      success: function (result) {
        Gemstone.navigate(result);
      },
      error: function () {
        gsNavigate("view_404");
      },
    });
  }
}

function gsFormPrev(containerId) {
  currentFormPage--;
  gsSlideFormPage(containerId);
}

function gsFormNext(containerId) {
  currentFormPage++;
  gsSlideFormPage(containerId);
}

function gsSlideFormPage(containerId) {
  let pages = document.querySelectorAll(
    "#" + containerId + " .gs-partial-form-page"
  );
  for (let i = 0; i < pages.length; i++) {
    let xShift = (currentFormPage - 1) * 100;
    if (currentFormPage - 1 == i) {
      pages[i].classList.remove("outside");
    } else {
      pages[i].classList.add("outside");
    }
    pages[i].style.transform = "translate3d(-" + xShift + "%, 0, 0)";
  }
}

function gsSubmitForm(formID, targetPage, target = false) {
  var is_valid = true;
  var error_message = "";
  var identic_value = "";
  $(".gs-form-warning").remove();
  $("#" + formID + " .required").each(function () {
    if (!this.value) {
      error_message = "Marked fields cannot be empty.";
      $(this).addClass("warning");
      is_valid = false;
    }
  });
  $("#" + formID + " .alphanumeric").each(function () {
    var allowed_value = /^[a-zA-Z0-9]+$/;
    if (!allowed_value.test(this.value)) {
      error_message += " Marked fields can only contain letter and number.";
      $(this).addClass("warning");
      is_valid = false;
    }
  });
  $("#" + formID + " .identic").each(function () {
    if (identic_value != "") {
      if (this.value != identic_value) {
        error_message += " Marked fields must contain the same value.";
        $(this).addClass("warning");
        is_valid = false;
      }
    }
    identic_value = this.value;
  });
  if (!is_valid) {
    $("#" + formID).prepend(
      "<div class='gs-form-warning'>" + error_message + "</div>"
    );
  } else {
    var targetURL = base_url + targetPage;
    if (target == false) {
      var form = document.getElementById(formID);
      var input = new FormData(form);
      input.append("ajax", "true");
      input.append("target", target);
      input.append("tabToken", tabToken);
      gsShowLoader();
      if (targetPage != "") {
        $.ajax({
          url: targetURL,
          type: "POST",
          data: input,
          processData: false,
          contentType: false,
          cache: false,
          success: function (result) {
            result.targetPage = targetPage;
            gsOnNavigateSuccess(result, targetPage);
          },
          error: function () {
            gsNavigate("view_404");
          },
        });
      }
    } else if (target == "_blank") {
      console.log("called");
      var form = document.getElementById(formID);
      form.setAttribute("action", targetURL);
      form.setAttribute("method", "post");
      form.setAttribute("target", "_blank");
      form.submit();
    }
  }
}

function gsSubmitFormCustom(
  controller_name,
  formID,
  targetPage,
  requireConfirmation,
  confirmMessage
) {
  var confirmation = false;
  var is_valid = true;
  var identic_value = "";
  $(".gs-form-warning").remove();
  if (requireConfirmation == true) {
    if (confirmMessage == "" || confirmMessage == null) {
      confirmMessage = "Are you sure you want to perform this action?";
    }
    confirmation = confirm(confirmMessage);
  } else {
    confirmation = true;
  }

  if (confirmation == true) {
    $("#" + formID + " .required").each(function () {
      if (!this.value) {
        error_message = "Marked fields cannot be empty.";
        $(this).addClass("warning");
        is_valid = false;
      }
    });
    $("#" + formID + " .alphanumeric").each(function () {
      var allowed_value = /^[a-zA-Z0-9]+$/;
      if (!allowed_value.test(this.value)) {
        error_message += " Marked fields can only contain letter and number.";
        $(this).addClass("warning");
        is_valid = false;
      }
    });
    $("#" + formID + " .identic").each(function () {
      if (identic_value != "") {
        if (this.value != identic_value) {
          error_message += " Marked fields must contain the same value.";
          $(this).addClass("warning");
          is_valid = false;
        }
      }
      identic_value = this.value;
    });
    if (!is_valid) {
      $("#" + formID).prepend(
        "<div class='gs-form-warning'>" + error_message + "</div>"
      );
    } else {
      var targetURL = custom_url + controller_name + "/" + targetPage;
      var form = document.getElementById(formID);
      var input = new FormData(form);
      input.append("ajax", "true");
      input.append("tabToken", tabToken);
      gsShowLoader();
      if (targetPage != "") {
        $.ajax({
          url: targetURL,
          type: "POST",
          data: input,
          processData: false,
          contentType: false,
          cache: false,
          success: function (result) {
            result.targetPage = targetPage;
            gsOnNavigateSuccess(result, targetPage);
          },
          error: function () {
            gsNavigate("view_404");
          },
        });
      }
    }
  }
}

function gsOnNavigateSuccess(result, targetPage) {
  Gemstone.closeAlert();
  primeContainer.html(result);
  primeContainer.removeClass("hidden");
  primeContainer.find(".gs-load-overlay").remove();
  gsMessageTimeout();
  gsCarouselInit();
  gsChartInit();
  gsCaptionInit();
  var index = targetPage.indexOf("?");
  if (index > -1) {
    targetPage = targetPage.substring(0, index);
  }
  for (var i = 0; i < buttonList.length; i++) {
    if (buttonList[i].getAttribute("data-target") == targetPage) {
      buttonList[i].classList.add("active");
    } else {
      buttonList[i].classList.remove("active");
    }
  }
  gsShowTab();
  gsBindListeners();
  MeasureConnectionSpeed();
  promptNavigate = false;
  extendSession = false;
  currentFormPage = 1;
  window.clearInterval(extendSessionInterval);

  Gemstone.promptNavigate = false;
  Gemstone.extendSession = false;
  Gemstone.currentFormPage = 1;
  window.clearInterval(Gemstone.extendSessionInterval);
}

function MeasureConnectionSpeed() {
  if (checkRequestCount >= checkRequestInterval) {
    var startTime, endTime;
    var lagTimer = window.setTimeout(function () {
      document.getElementById("gsInetWarning").classList.remove("hidden");
    }, 1000);
    var download = new Image();
    download.onload = function () {
      window.clearTimeout(lagTimer);
      endTime = new Date().getTime();
      showResults();
    };

    download.onerror = function (err, msg) {
      console.log("error testing speed.");
    };

    startTime = new Date().getTime();
    download.src = checkImageUrl + "?acv=" + startTime;
    function showResults() {
      var duration = (endTime - startTime) / 1000;
      var bitsLoaded = checkImageSize * 8; //download size in bytes
      var speedBps = (bitsLoaded / duration).toFixed(2);
      var speedKbps = (speedBps / 1024).toFixed(2);
      var speedMbps = (speedKbps / 1024).toFixed(2);
      document.getElementById("gsInetSpeedLabel").innerHTML =
        " (" + numberFormat(speedMbps) + " Mbps)";
      if (speedMbps < 0.5) {
        document.getElementById("gsInetWarning").classList.remove("hidden");
      } else {
        document.getElementById("gsInetWarning").classList.add("hidden");
      }
    }
    checkRequestCount = 0;
  } else {
    checkRequestCount++;
  }
}

function gsExtendSession() {
  let domForm = document.getElementsByClassName("gs-autosave");
  var savedData = false;
  var methodName = false;
  if (domForm != null) {
    let keys = [];
    let values = [];
    domForm.forEach((item) => {
      let formData = new FormData(item);
      for (var pair of formData.entries()) {
        keys.push(pair[0]);
        values.push(pair[1]);
      }
    });
    savedData = {};
    keys.forEach((key, i) => (savedData[key] = values[i]));
    savedData = JSON.stringify(savedData);

    let breadcrumbLink = document.querySelectorAll(".gs-breadcrumb-link");
    let rawLink =
      breadcrumbLink[breadcrumbLink.length - 1].getAttribute("onclick");
    rawLink += "?";
    methodName = rawLink.substring(
      rawLink.indexOf('"') + 1,
      rawLink.indexOf("?")
    );
  }
  $.ajax({
    url: base_url + "extend_session",
    type: "POST",
    data: {
      form: savedData,
      method: methodName,
      ajax: true,
      tabToken,
    },
    success: function (result) {
      console.log("Session extended while completing form.");
    },
    error: function () {
      console.log("Faile dto extend session.");
    },
  });
}

function gsBindListeners(scrollToTop = false) {
  $("input, textarea, select").on("change", function () {
    if (this.classList.contains("no-prompt")) {
      //do not prompt or extend session
    } else {
      Gemstone.promptNavigate = true;
      promptNavigate = true;
      if (extendSession == false) {
        extendSession = true;
        extendSessionInterval = window.setInterval(function () {
          gsExtendSession();
        }, 300000);
      }
    }
  });
  $("input").on("focus", function () {
    $(this).removeClass("warning");
  });
  $(".gs-file-input").on("change", function (e) {
    gsFileInputUX(e.target);
  });
  $(".gs-dropdown-trigger").on("click", gsToggleDropdown);
  $("textarea").on("focus", function () {
    $(this).removeClass("warning");
  });
  var number_input = $(".number-format");
  for (var i = 0; i < number_input.length; i++) {
    number_input[i].value = numberFormat(number_input[i].value);
  }
  number_input.on("keyup", function (e) {
    if (e.keyCode !== 8 && e.keyCode !== 46) {
      this.value = numberFormat(this.value);
    }
  });
  if (scrollToTop === true) {
    window.setTimeout(function () {
      $("body, html, #gs-content").animate({ scrollTop: 0 }, "fast");
    }, 10);
  }
  var buttons = document.querySelectorAll(
    ".gs-button, .gs-floating-button, .gs-buttonbar-button, .gs-popup-button, .gs-table-button"
  );
  if (buttons) {
    for (i = 0; i < buttons.length; i++) {
      buttons[i].setAttribute("tabindex", "0");
      buttons[i].removeEventListener("keyup", gsClickButton);
      buttons[i].addEventListener("keyup", gsClickButton);
    }
  }
  $(".gs-tab-nav-button").on("click", gsToggleTab);
}

function gsClickButton(e) {
  if (e.keyCode == 32 || e.keyCode == 13) {
    this.click();
  }
}

function gsTimeFormat(value) {
  var seconds = parseInt(value);
  var days = Math.floor(seconds / (3600 * 24));
  seconds -= days * 3600 * 24;
  var hrs = Math.floor(seconds / 3600);
  seconds -= hrs * 3600;
  var mnts = Math.floor(seconds / 60);
  seconds -= mnts * 60;
  return days + " Hari " + hrs + " Jam " + mnts + " Menit ";
}

function gsNumberFormat(value, currency) {
  if (currency != undefined) {
    return "" + currency + numberFormat(value) + ",-";
  } else {
    return numberFormat(value);
  }
}

function numberFormat(value) {
  if (value.toString()[0] == "-") {
    var negative = "-";
  } else {
    negative = "";
  }
  var raw = value
    .toString()
    .replace(/(?!\.)\D/g, "")
    .split(".");
  var whole = raw[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  var decimal = false;
  if (raw.length > 1) {
    decimal = raw[1];
  }
  if (decimal !== false && (decimal == "" || parseInt(decimal) > 0)) {
    return negative + whole + "." + decimal;
  } else {
    return negative + whole;
  }
}

function gsMessageTimeout() {
  window.clearTimeout(messageTimer);
  messageTimer = setTimeout(function () {
    $(".gs-message").addClass("fade");
  }, messageTimeout);
}

function gsEnterOverride(e, formID, targetPage) {
  if (e.keyCode === 13) {
    e.preventDefault();
    if (formID != undefined && targetPage != undefined) {
      gsSubmitForm(formID, targetPage);
    }
  }
}

function gsQuickSearch(
  inputTextID,
  targetPage,
  containerID,
  keyboardEvent = false,
  callback = false
) {
  var process_search = false;
  var container = $("#" + containerID);
  var focusedInput = document.getElementsByClassName("qs-focus");
  for (let i = 0; i < focusedInput.length; i++) {
    focusedInput[i].classList.remove("qs-focus");
  }
  container.addClass("qs-focus");
  if (keyboardEvent) {
    if (keyboardEvent.keyCode >= 37 && keyboardEvent.keyCode <= 40) {
      //arrow navigation
      var items = container.find(".gs-qs-item");
      if (items.length > 0) {
        var max_index = items.length - 1;
        var previous = false;
        for (var i = 0; i <= max_index; i++) {
          if (items[i].classList.contains("selected")) {
            previous = true;
            selected_index = i;
            //console.log("selected on index: " + i);
            items[i].classList.remove("selected");
          }
        }
        //console.log("was on index: " + selected_index + "/" + max_index);
        if (previous == true) {
          if (keyboardEvent.keyCode == 40) {
            selected_index++;
            //console.log("Index Increased to: " + selected_index);
          } else if (keyboardEvent.keyCode == 38) {
            selected_index--;
            //console.log("Index Decreased to: " + selected_index);
          }
        } else {
          selected_index = 0;
        }
        if (selected_index < 0) {
          selected_index = 0;
        }

        if (selected_index > max_index) {
          selected_index = max_index;
        }
        items[selected_index].classList.add("selected");
      }
    } else if (keyboardEvent.keyCode == 13) {
      //enter hit
      var selected = container.find(".selected");
      if (selected.length > 0) {
        selected[0].click();
      }
    } else {
      process_search = true;
    }
  } else {
    process_search = true;
  }

  if (process_search == true) {
    //console.log("searching...");
    var keyword = $("#" + inputTextID).val();
    let controller = base_url;
    if (targetPage.includes("/")) {
      controller = custom_url;
    }
    const targetURL = controller + targetPage;
    window.clearTimeout(qstimer);
    qstimer = setTimeout(function () {
      $.ajax({
        url: targetURL,
        type: "POST",
        data: {
          keyword: keyword,
          ajax: true,
          tabToken,
        },
        success: function (data) {
          container.html(data);
          if (callback != false) {
            callback();
          }
        },
      });
    }, qsDelay);
  }
}

function gsCurrencyFormat(e, target) {
  // skip for arrow keys
  if (
    (e.keyCode >= 37 && e.keyCode <= 40) ||
    e.keyCode == 8 ||
    e.keyCode == 46
  ) {
    return;
  }
  // format number
  setTimeout(function () {
    $(target).val(function (index, value) {
      return value
        .replace(/(?!\.)\D/g, "")
        .replace(/(?=\..*)\./g, "")
        .replace(/(?=\.\d\d).*/g, "")
        .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    });
  }, 500);
}

function gsOpenModal(targetID) {
  $("#" + targetID).addClass("active");
  $("#" + targetID + " input")
    .first()
    .focus();
}

function gsCloseModal() {
  $(".gs-modal-overlay").removeClass("active");
}

function gsAlert(type, message, title, actions = false) {
  var html = "";
  if (title == "" || title == null) {
    title = type;
    title = title.charAt(0).toUpperCase() + title.slice(1);
  }
  html += "<div class='gs-popup-overlay' id='alertPop'>";
  html += "<div class='gs-popup-container'>";
  html += "<div class='gs-popup-title'>" + title + "</div>";
  html += "<div class='gs-popup-message'>" + message + "</div>";
  html += "<div class='gs-popup-button-container'>";
  if (actions != false && Array.isArray(actions)) {
    for (var i = 0; i < actions.length; i++) {
      if (
        actions[i].label !== undefined &&
        (typeof actions[i].action !== "undefined" ||
          typeof actions[i].link !== "undefined")
      ) {
        if (typeof actions[i].type === "undefined") {
          actions[i].type = "primary";
        }
        if (typeof actions[i].icon === "undefined") {
          actions[i].icon = "check";
        }
        if (typeof actions[i].action !== "undefined") {
          html +=
            "<div class='gs-popup-button " +
            actions[i].type +
            "' onclick='" +
            actions[i].action +
            "'><i class='gs-icon " +
            actions[i].icon +
            "'></i><p>" +
            actions[i].label +
            "</p></div>";
        } else {
          html +=
            "<div class='gs-popup-button " +
            actions[i].type +
            "' onclick='gsNavigate(\"" +
            actions[i].link +
            "\")'><i class='gs-icon " +
            actions[i].icon +
            "'></i><p>" +
            actions[i].label +
            "</p></div>";
        }
      }
    }
  }
  html +=
    "<div class='gs-popup-button' onclick='gsCloseAlert()'><i class='gs-icon close'></i><p>Close</p></div>";
  html += "</div>";
  html += "</div>";
  html += "</div>";
  $("body").append(html);
}

function gsPrompt(message, title, actions = false) {
  var html = "";
  if (title == "" || title == null) {
    title = type;
    title = title.charAt(0).toUpperCase() + title.slice(1);
  }
  html += "<div class='gs-popup-overlay' id='alertPop'>";
  html += "<div class='gs-popup-container'>";
  html += "<div class='gs-popup-title'>" + title + "</div>";
  html += "<div class='gs-popup-message'>" + message + "</div>";
  html += "<div class='gs-popup-button-container'>";
  if (actions != false && Array.isArray(actions)) {
    for (var i = 0; i < actions.length; i++) {
      if (
        actions[i].label !== undefined &&
        (typeof actions[i].action !== "undefined" ||
          typeof actions[i].link !== "undefined")
      ) {
        if (typeof actions[i].type === "undefined") {
          actions[i].type = "primary";
        }
        if (typeof actions[i].icon === "undefined") {
          actions[i].icon = "check";
        }
        if (typeof actions[i].action !== "undefined") {
          html +=
            "<div class='gs-popup-button " +
            actions[i].type +
            "' onclick='" +
            actions[i].action +
            "'><i class='gs-icon " +
            actions[i].icon +
            "'></i><p>" +
            actions[i].label +
            "</p></div>";
        } else {
          html +=
            "<div class='gs-popup-button " +
            actions[i].type +
            "' onclick='gsNavigate(\"" +
            actions[i].link +
            "\")'><i class='gs-icon " +
            actions[i].icon +
            "'></i><p>" +
            actions[i].label +
            "</p></div>";
        }
      }
    }
  }
  html +=
    "<div class='gs-popup-button'><i class='gs-icon close'></i><p>Close</p></div>";
  html += "</div>";
  html += "</div>";
  html += "</div>";
  $("body").append(html);
  $(".gs-popup-button").on("click", function () {
    gsCloseAlert();
  });
  gsBindListeners();
}

function gsCloseAlert() {
  $("#alertPop").remove();
}

function gsClosePopup() {
  var target = $(".gs-popup-overlay");
  target.remove();
}

function gsSidebarNavigate() {
  var targetLink = $(this).attr("data-target");
  $(".gs-sidebar-button").removeClass("active");
  $(this).addClass("active");
  gsCloseSidebar();
  gsNavigate(targetLink);
}

function gsToggleSidebarGroup() {
  $(this).parent().toggleClass("expanded");
}

function gsToggleSidebar() {
  $(".gs-sidebar-wrapper").toggleClass("expanded");
}

function gsMinimizeSidebar() {
  if (is_maxed == false) {
    is_maxed = true;
  } else {
    is_maxed = false;
  }
  $(".gs-sidebar-wrapper").toggleClass("minimized");
  $(".gs-prime-wrapper").toggleClass("maximized");
  $(".gs-sidebar-button-group").removeClass("expanded");
  window.dispatchEvent(new Event("resize"));
}

function gsCloseSidebar() {
  $(".gs-sidebar-wrapper").removeClass("expanded");
}

function gsToggleTab() {
  //    var targetElement = $("#" + $(this).attr("tab-id"));
  if (!this.classList.contains("dummy")) {
    var targetElement = document.getElementById(this.getAttribute("tab-id"));
    $(".gs-tab-nav-button").removeClass("active");
    $(".gs-tab-page").removeClass("active");
    this.classList.add("active");
    targetElement.classList.add("active");
    var tab_index = 1;
    var tabNavs = document.getElementsByClassName("gs-tab-nav-button");
    for (var i = 0; i < tabNavs.length; i++) {
      if (tabNavs[i].getAttribute("tab-id") == this.getAttribute("tab-id")) {
        tab_index = i + 1;
      }
    }
    var xhr = new XMLHttpRequest();
    var data = gsUrlEncodeData({ tab: tab_index, tabToken });
    xhr.open("POST", base_url + "update_tab", true);
    xhr.setRequestHeader(
      "Content-Type",
      "application/x-www-form-urlencoded; charset=UTF-8"
    );
    xhr.onload = function () {
      if (xhr.status === 200) {
      } else {
      }
    };
    xhr.send(data);
  }
}

function gsUrlEncodeData(data) {
  var urlEncodedData = "";
  var urlEncodedDataPairs = [];
  var name;
  // Turn the data object into an array of URL-encoded key/value pairs.
  for (name in data) {
    urlEncodedDataPairs.push(
      encodeURIComponent(name) + "=" + encodeURIComponent(data[name])
    );
  }

  // Combine the pairs into a single string and replace all %-encoded spaces to
  // the '+' character; matches the behaviour of browser form submissions.
  urlEncodedData = urlEncodedDataPairs.join("&").replace(/%20/g, "+");
  return urlEncodedData;
}

function gsShowTab() {
  var tab_helper = document.getElementsByClassName("gs-tab-helper");
  if (tab_helper.length > 0) {
    var active_index = tab_helper[0].getAttribute("active-tab");
    var nav_list = document.getElementsByClassName("gs-tab-nav-button");
    var page_list = document.getElementsByClassName("gs-tab-page");
    for (i = 0; i < nav_list.length; i++) {
      if (i == active_index - 1) {
        nav_list[i].classList.add("active");
        if (!nav_list[i].classList.contains("dummy")) {
          document
            .getElementById(nav_list[i].getAttribute("tab-id"))
            .classList.add("active");
          page_list[i].classList.add("active");
        }
      } else {
        nav_list[i].classList.remove("active");
        if (!nav_list[i].classList.contains("dummy")) {
          document
            .getElementById(nav_list[i].getAttribute("tab-id"))
            .classList.remove("active");
        }
      }
    }
  }
}

function gsFileInputUX(target) {
  var label = target.nextElementSibling;
  var labelVal = label.innerHTML;
  var fileName = "";
  if (target.files.length > 0) {
    if (target.files.length > 1) {
      fileName = target.files.length + " files selected";
    } else {
      fileName = target.files[0].name;
    }
  }
  if (fileName != "") {
    label.innerHTML = fileName;
  } else {
    label.innerHTML = labelVal;
  }
}

function gsReplaceMissingImage(target) {
  target.onerror = null;
  target.src = "gemstone_core/image/image-placeholder.png";
}

function gsChartInit() {
  var target = document.getElementsByClassName("gs-chart");
  for (var i = 0; i < target.length; i++) {
    gsChart(target[i]);
  }
}

function gsChart(container) {
  gsRestructureChart(container);
}

function gsRestructureChart(container) {
  var container_width = window.getComputedStyle(container).width;
  var chart_type = container.getAttribute("type");
  var wrapper = document.createElement("div");
  wrapper.className = "gs-chart-wrapper";
  var title = document.createElement("div");
  title.className = "gs-chart-title";
  title.innerHTML = container.getAttribute("title");
  wrapper.appendChild(title);
  var chart_area = document.createElement("div");
  chart_area.className = "gs-chart-container " + chart_type;
  var legend_area = document.createElement("div");
  legend_area.className = "gs-chart-legend-container";
  var label_count = 0;
  var hue = Math.floor(Math.random() * 260) + 100;
  var sat = Math.floor(Math.random() * 30);
  var max_height = container.getAttribute("height");
  var max_value = 0;
  var min_value = 99999999999;
  chart_area.style.minHeight = max_height;
  for (var i = 0; i < container.childNodes.length; i++) {
    if (container.childNodes[i].nodeType === 1) {
      var compare = parseInt(container.childNodes[i].getAttribute("value"));
      if (compare >= max_value) {
        max_value = compare;
      }
      if (compare <= min_value) {
        min_value = compare;
      }
      label_count++;
    }
  }
  while (container.childNodes[0]) {
    if (container.childNodes[0].nodeType === 1) {
      var item = document.createElement("div");
      var label_value = container.childNodes[0].getAttribute("value");
      item.className = "gs-chart-item " + chart_type;
      var calculated_height = (parseInt(label_value) / max_value) * max_height;
      if (calculated_height <= 0) {
        calculated_height = 1;
      }
      if (calculated_height <= 20) {
        item.className = "gs-chart-item " + chart_type + " low";
      }
      item.style.height = calculated_height + "px";
      hue += Math.floor(Math.random() * 20);
      sat += Math.floor(Math.random() * 20);
      if (chart_type == "bar") {
        item.style.backgroundColor = "hsl(" + hue + ", " + sat + "%, 50%)";
      }
      item.setAttribute("label", gsNumberFormat(label_value));
      item.setAttribute(
        "onclick",
        container.childNodes[0].getAttribute("link")
      );
      if (chart_type == "line") {
        if (container.childNodes[0].getAttribute("last")) {
        } else {
          var next = container.childNodes[0].getAttribute("next");
          var next_calculated_height =
            (parseInt(next) / max_value) * max_height;
          var height_diff = next_calculated_height - calculated_height;
          var item_width = parseFloat(container_width) / label_count;
          var degree =
            (-1 * Math.atan(height_diff / item_width) * 180) / Math.PI;
          var line_width = Math.sqrt(
            Math.pow(height_diff, 2) + Math.pow(item_width, 2)
          );
          var line = document.createElement("div");
          line.className = "line";
          line.style.width = line_width + "px";
          item.style.transform = "rotateZ(" + degree + "deg)";
          container.childNodes[0].style.transform =
            "rotateZ(" + degree * -1 + "deg)";
          item.appendChild(line);
        }
        container.childNodes[0].setAttribute(
          "value",
          gsNumberFormat(container.childNodes[0].getAttribute("value"))
        );
      }
      var legend_item = document.createElement("div");
      legend_item.className = "gs-chart-legend-item";
      legend_item.innerHTML = container.childNodes[0].innerHTML;
      legend_area.appendChild(legend_item);
      container.childNodes[0].innerHTML = "";
      item.appendChild(container.childNodes[0]);
      chart_area.appendChild(item);
    } else {
      container.removeChild(container.childNodes[0]);
    }
  }
  chart_area.style.gridTemplateColumns = "repeat(" + label_count + ", 1fr)";
  legend_area.style.gridTemplateColumns = "repeat(" + label_count + ", 1fr)";
  wrapper.appendChild(chart_area);
  wrapper.appendChild(legend_area);
  container.appendChild(wrapper);
}

function gsCarouselInit() {
  var target = document.getElementsByClassName("gs-carousel");
  for (var i = 0; i < target.length; i++) {
    gsCarousel(target[i], false, 400);
  }
}

function gsCarousel(container, resize = false, max_width = 0) {
  gsRestructureCarousel(container, resize, max_width);
  var container_width = container.clientWidth;
  var wrapper = container.querySelector(".gs-carousel-wrapper");
  var wrapper_width = wrapper.offsetWidth;
  var items = container.querySelectorAll(".gs-carousel-item");
  var active_item = items[0];
  var slide_treshold = 30;
  var is_dragging = false;
  var is_touch = false;
  var start_x = 0;
  var start_y = 0;
  var diff_x = 0;
  var diff_y = 0;
  var current_index = 0;
  var current_x = 0;
  var current_y = 0;
  var max_index = items.length - 1;
  active_item.className = "gs-carousel-item active";
  container.addEventListener("touchstart", gsCarouselStartDrag);
  container.addEventListener("touchend", gsCarouselEndDrag);
  container.addEventListener("touchmove", gsCarouselDrag);
  container.addEventListener("mousedown", gsCarouselStartDrag);
  container.addEventListener("mouseup", gsCarouselEndDrag);
  container.addEventListener("mousemove", gsCarouselDrag);
  container.addEventListener("mouseleave", gsCarouselEndDrag);
  function gsCarouselStartDrag() {
    event.preventDefault();
    if (is_dragging == false) {
      is_dragging = true;
      wrapper.className = "gs-carousel-wrapper";
      if (event.touches) {
        is_touch = true;
        start_x = event.touches[0].clientX;
        start_y = event.touches[0].clientY;
      } else {
        is_touch = false;
        start_x = event.clientX;
        start_y = event.clientY;
      }
    }
  }

  function gsCarouselEndDrag() {
    if (is_dragging) {
      is_dragging = false;
      wrapper.className = "gs-carousel-wrapper animate";
      var end_x = "";
      var end_y = "";
      if (Math.abs(diff_x) > container_width * (slide_treshold / 100)) {
        if (diff_x > 0) {
          current_index--;
        } else {
          current_index++;
        }
        if (current_index > max_index) {
          current_index = max_index;
        }
        if (current_index < 0) {
          current_index = 0;
        }
      }
      current_x = -(current_index * container_width);
      wrapper.style.transform = "translate3d(" + current_x + "px, 0, 0)";
      var previous_active = container.querySelector(".gs-carousel-item.active");
      previous_active.className = "gs-carousel-item";
      active_item = items[current_index];
      active_item.className = "gs-carousel-item active";
    }
  }

  function gsCarouselDrag() {
    if (is_dragging) {
      if (is_touch) {
        diff_x = event.touches[0].clientX - start_x;
        diff_y = event.touches[0].clientY - start_y;
      } else {
        diff_x = event.clientX - start_x;
        diff_y = event.clientY - start_y;
      }
      var preview_x = diff_x + current_x;
      var preview_y = diff_y + current_y;
      //            wrapper.style.transform = "translate3d(" + preview_x + "px, " + preview_y + "px, 0)";
      wrapper.style.transform = "translate3d(" + preview_x + "px, 0, 0)";
    }
  }
}

function gsRestructureCarousel(container, resize = false, max_width = 0) {
  var container_width = parseFloat(window.getComputedStyle(container).width);
  if (max_width > 0 && container_width > max_width) {
    container.style.maxWidth = max_width;
  }
  if (resize === false) {
    var wrapper = document.createElement("div");
    wrapper.className = "gs-carousel-wrapper";
    while (container.childNodes[0]) {
      if (container.childNodes[0].nodeType === 1) {
        var item = document.createElement("div");
        item.className = "gs-carousel-item";
        if (max_width > 0 && container_width > max_width) {
          container_width = max_width + "px";
        }
        item.style.width = container_width;
        item.style.height = container_width;
        item.appendChild(container.childNodes[0]);
        wrapper.appendChild(item);
      } else {
        container.removeChild(container.childNodes[0]);
      }
    }
    container.appendChild(wrapper);
  } else {
    container.style.width = "0";
    container.style.maxWidth = "none";
    container.style.width = "auto";
    container_width = parseFloat(window.getComputedStyle(container).width);
    if (max_width > 0 && container_width > max_width) {
      container.style.maxWidth = max_width;
      container_width = max_width + "px";
    }
    var list = document.getElementsByClassName("gs-carousel-item");
    for (var i = 0; i < list.length; i++) {
      list[i].style.flex = "0 0 " + container_width;
    }
  }

  var images = container.getElementsByTagName("img");
  for (var i = 0; i < images.length; i++) {
    images[i].onload = function () {
      if (this.height >= this.width) {
        this.className = "tall";
      } else {
        this.className = "wide";
      }
    };
  }
}

function gsCarouselResize() {
  var target = document.getElementsByClassName("gs-carousel");
  for (var i = 0; i < target.length; i++) {
    gsCarousel(target[i], true, 400);
  }
}

function gsCaptionInit() {
  var target = document.getElementsByClassName("gs-caption");
  for (var i = 0; i < target.length; i++) {
    target[i].addEventListener("mouseover", gsAlignCaption);
  }
}

function gsAlignCaption() {
  if (window.innerWidth - event.clientX <= 200) {
    this.classList.add("gs-caption-right");
  } else {
    this.classList.remove("gs-caption-right");
  }
}

function gsGetUniqueID(prefix = false) {
  var d = new Date();
  if (prefix === false) {
    prefix = "";
  }
  var row_id =
    prefix +
    d.getHours() +
    d.getMinutes() +
    d.getSeconds() +
    d.getMilliseconds() +
    Math.floor(Math.random() * 1000).toString();
  return row_id;
}

function gsToggleDropdown() {
  this.nextSibling.nextSibling.classList.toggle("expanded");
}

function gsRefocusInput(inputID) {
  var inputField = document.getElementById(inputID);
  inputField.focus();
  var tempValue = inputField.value;
  inputField.value = "";
  inputField.value = tempValue;
}

function gsValidPrintableKey(keyCode) {
  var valid =
    (keyCode > 47 && keyCode < 58) || // number keys
    keyCode == 32 ||
    keyCode == 13 || // spacebar & return key(s) (if you want to allow carriage returns)
    (keyCode > 64 && keyCode < 91) || // letter keys
    (keyCode > 95 && keyCode < 112) || // numpad keys
    (keyCode > 185 && keyCode < 193) || // ;=,-./` (in order)
    (keyCode > 218 && keyCode < 223); // [\]' (in order)
  return valid;
}

function gsGetCurrentTabIndex() {
  let tabList = document.getElementsByClassName("gs-tab-nav-button");
  let tab = 1;
  for (i = 0; i < tabList.length; i++) {
    if (tabList[i].classList.contains("active")) {
      tab = i + 1;
    }
  }
  return tab;
}

function gsGetActiveTabIndex() {
  return gsGetCurrentTabIndex();
}

function warnHiddenInputEmpty(hiddenInputId, textInputId) {
  var hiddenInput = document.getElementById(hiddenInputId);
  var textInput = document.getElementById(textInputId);
  if (hiddenInput.value == "" || hiddenInput.value == 0) {
    textInput.style.borderColor = "var(--color-bad)";
  } else {
    textInput.style.borderColor = "var(--color-good)";
  }
}

function getLastPage(allowPrompt = true) {
  $.ajax({
    url: base_url + "view_last_page",
    type: "POST",
    data: {
      ajax: true,
      tabToken,
    },
    success: function (result) {
      if (allowPrompt === false) {
        Gemstone.promptNavigate = false;
      }
      Gemstone.navigate(result);
    },
    error: function () {
      gsNavigate("view_404");
    },
  });
}

function customFormat(value, precision = 2) {
  var negative = value < 0 ? "-" : "";

  // Extract the integer and decimal parts
  var parts = Math.abs(value).toFixed(precision).split(".");
  var whole = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  var decimal = parts[1];

  // Check if there is a non-zero decimal part
  if (decimal !== undefined && (decimal === "" || parseInt(decimal) > 0)) {
    return negative + whole + "." + decimal;
  } else {
    return negative + whole;
  }
}
