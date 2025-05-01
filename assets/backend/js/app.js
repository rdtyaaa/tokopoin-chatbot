!(function () {
  var o, s, d, t, n, e, i, m, c;

  // ===============SideBar Start
  function f() {
    var e;
    document.querySelectorAll(".navbar-nav .collapse") &&
      ((e = document.querySelectorAll(".navbar-nav .collapse")),
      Array.from(e).forEach(function (t) {
        var a = new bootstrap.Collapse(t, { toggle: !1 });
        t.addEventListener("show.bs.collapse", function (e) {
          e.stopPropagation();
          var e = t.parentElement.closest(".collapse");
          e
            ? ((e = e.querySelectorAll(".collapse")),
              Array.from(e).forEach(function (e) {
                e = bootstrap.Collapse.getInstance(e);
                e !== a && e.hide();
              }))
            : ((e = (function (e) {
                for (var t = [], a = e.parentNode.firstChild; a; )
                  1 === a.nodeType && a !== e && t.push(a), (a = a.nextSibling);
                return t;
              })(t.parentElement)),
              Array.from(e).forEach(function (e) {
                2 < e.childNodes.length &&
                  e.firstElementChild.setAttribute("aria-expanded", "false");
                e = e.querySelectorAll("*[id]");
                Array.from(e).forEach(function (e) {
                  e.classList.remove("show"),
                    2 < e.childNodes.length &&
                      ((e = e.querySelectorAll("ul li a")),
                      Array.from(e).forEach(function (e) {
                        e.hasAttribute("aria-expanded") &&
                          e.setAttribute("aria-expanded", "false");
                      }));
                });
              }));
        }),
          t.addEventListener("hide.bs.collapse", function (e) {
            e.stopPropagation();
            e = t.querySelectorAll(".collapse");
            Array.from(e).forEach(function (e) {
              (childCollapseInstance =
                bootstrap.Collapse.getInstance(e)).hide();
            });
          });
      }));
  }
  // ===============SideBar End

  function I() {

    var e = document.documentElement.clientWidth,
      e =
        (e < 1025 && 767 < e
          ? (document.body.classList.remove("twocolumn-panel"),
            "twocolumn" == sessionStorage.getItem("data-layout-default") &&
              (document.documentElement.setAttribute(
                "data-layout-default",
                "twocolumn"
              ),
              document.getElementById("customizer-layout03") &&
                document.getElementById("customizer-layout03").click(),
              A(),
              f()),
            document.querySelector(".hamburger-btn") &&
              document.querySelector(".hamburger-btn").classList.add("open"))
          : 1025 <= e
          ? (document.body.classList.remove("twocolumn-panel"),
            "twocolumn" == sessionStorage.getItem("data-layout-default") &&
              (document.documentElement.setAttribute(
                "data-layout-default",
                "twocolumn"
              ),
              document.getElementById("customizer-layout03") &&
                document.getElementById("customizer-layout03").click(),
              A(),
              f()),
            "vertical" == sessionStorage.getItem("data-layout-default") &&
              document.documentElement.setAttribute(
                "data-sidebar-size",
                sessionStorage.getItem("data-sidebar-size")
              ),
            document.querySelector(".hamburger-btn") &&
              document.querySelector(".hamburger-btn").classList.remove("open"))
          : e <= 767 &&
            (document.body.classList.remove("vertical-sidebar-enable"),
            document.body.classList.add("twocolumn-panel"),
            "twocolumn" == sessionStorage.getItem("data-layout-default") &&
              (document.documentElement.setAttribute(
                "data-layout-default",
                "vertical"
              ),
              k("vertical"),
              f()),
            "horizontal" != sessionStorage.getItem("data-layout-default") &&
              document.documentElement.setAttribute("data-sidebar-size", "lg"),
            document.querySelector(".hamburger-btn")) &&
            document.querySelector(".hamburger-btn").classList.add("open"),
        document.querySelectorAll("#navbar-nav > li.nav-item"));
    Array.from(e).forEach(function (e) {
      e.addEventListener("click", w.bind(this), !1),
        e.addEventListener("mouseover", w.bind(this), !1);
    });
  }

  function w(e) {
    if (e.target && e.target.matches("a.nav-link span"))
      if (0 == v(e.target.parentElement.nextElementSibling)) {
        e.target.parentElement.nextElementSibling.classList.add(
          "dropdown-custom-right"
        ),
          e.target.parentElement.parentElement.parentElement.parentElement.classList.add(
            "dropdown-custom-right"
          );
        var t = e.target.parentElement.nextElementSibling;
        Array.from(t.querySelectorAll(".menu-dropdown")).forEach(function (e) {
          e.classList.add("dropdown-custom-right");
        });
      } else if (
        1 == v(e.target.parentElement.nextElementSibling) &&
        1848 <= window.innerWidth
      )
        for (
          var a = document.getElementsByClassName("dropdown-custom-right");
          0 < a.length;

        )
          a[0].classList.remove("dropdown-custom-right");
    if (e.target && e.target.matches("a.nav-link"))
      if (0 == v(e.target.nextElementSibling)) {
        e.target.nextElementSibling.classList.add("dropdown-custom-right"),
          e.target.parentElement.parentElement.parentElement.classList.add(
            "dropdown-custom-right"
          );
        t = e.target.nextElementSibling;
        Array.from(t.querySelectorAll(".menu-dropdown")).forEach(function (e) {
          e.classList.add("dropdown-custom-right");
        });
      } else if (
        1 == v(e.target.nextElementSibling) &&
        1848 <= window.innerWidth
      )
        for (
          a = document.getElementsByClassName("dropdown-custom-right");
          0 < a.length;

        )
          a[0].classList.remove("dropdown-custom-right");
  }

  function M() {
    var e = document.documentElement.clientWidth;
    767 < e &&
      document.querySelector(".hamburger-btn").classList.toggle("open"),
      "vertical" ===
        document.documentElement.getAttribute("data-layout-default") &&
        (e < 1025 && 767 < e
          ? (document.body.classList.remove("vertical-sidebar-enable"),
            "sm" == document.documentElement.getAttribute("data-sidebar-size")
              ? document.documentElement.setAttribute("data-sidebar-size", "")
              : document.documentElement.setAttribute(
                  "data-sidebar-size",
                  "sm"
                ))
          : 1025 < e
          ? (document.body.classList.remove("vertical-sidebar-enable"),
            "lg" == document.documentElement.getAttribute("data-sidebar-size")
              ? document.documentElement.setAttribute("data-sidebar-size", "sm")
              : document.documentElement.setAttribute(
                  "data-sidebar-size",
                  "lg"
                ))
          : e <= 767 &&
            (document.body.classList.add("vertical-sidebar-enable"),
            document.documentElement.setAttribute("data-sidebar-size", "lg")));
  }

  function O() {
    window.addEventListener("resize", I),
      I(),
      document.addEventListener("scroll", function () {
        var e;
        (e = document.getElementById("header")) &&
          (50 <= document.body.scrollTop ||
          50 <= document.documentElement.scrollTop
            ? e.classList.add("topbar-shadow")
            : e.classList.remove("topbar-shadow"));
      });

    window.addEventListener("load", function () {
      var e;
      ("twocolumn" ==
        document.documentElement.getAttribute("data-layout-default")
        ? A
        : L)(),
        (e = document.getElementsByClassName("vertical-overlay")) &&
          Array.from(e).forEach(function (e) {
            e.addEventListener("click", function () {
              document.body.classList.remove("vertical-sidebar-enable"),
                "twocolumn" == sessionStorage.getItem("data-layout-default")
                  ? document.body.classList.add("twocolumn-panel")
                  : document.documentElement.setAttribute(
                      "data-sidebar-size",
                      sessionStorage.getItem("data-sidebar-size")
                    );
            });
          });
    }),
      document.getElementById("hamburger-btn") &&
        document.getElementById("hamburger-btn").addEventListener("click", M);
  }

  function A() {
    var e,
      t,
      a =
        "/" == location.pathname
          ? "index.html"
          : location.pathname.substring(1);
    (a = a.substring(a.lastIndexOf("/") + 1)) &&
      ("twocolumn-panel" == document.body.className &&
        document
          .getElementById("two-column-menu")
          .querySelector('[href="' + a + '"]')
          .classList.add("active"),
      (a = document
        .getElementById("navbar-nav")
        .querySelector('[href="' + a + '"]'))
        ? (a.classList.add("active"),
          (t = (
            (e = a.closest(".collapse.menu-dropdown")) &&
            e.parentElement.closest(".collapse.menu-dropdown")
              ? (e.classList.add("show"),
                e.parentElement.children[0].classList.add("active"),
                e.parentElement
                  .closest(".collapse.menu-dropdown")
                  .parentElement.classList.add("twocolumn-item-show"),
                e.parentElement.parentElement.parentElement.parentElement.closest(
                  ".collapse.menu-dropdown"
                ) &&
                  ((t =
                    e.parentElement.parentElement.parentElement.parentElement
                      .closest(".collapse.menu-dropdown")
                      .getAttribute("id")),
                  e.parentElement.parentElement.parentElement.parentElement
                    .closest(".collapse.menu-dropdown")
                    .parentElement.classList.add("twocolumn-item-show"),
                  e.parentElement
                    .closest(".collapse.menu-dropdown")
                    .parentElement.classList.remove("twocolumn-item-show"),
                  document
                    .getElementById("two-column-menu")
                    .querySelector('[href="#' + t + '"]')) &&
                  document
                    .getElementById("two-column-menu")
                    .querySelector('[href="#' + t + '"]')
                    .classList.add("active"),
                e.parentElement.closest(".collapse.menu-dropdown"))
              : (a
                  .closest(".collapse.menu-dropdown")
                  .parentElement.classList.add("twocolumn-item-show"),
                e)
          ).getAttribute("id")),
          document
            .getElementById("two-column-menu")
            .querySelector('[href="#' + t + '"]') &&
            document
              .getElementById("two-column-menu")
              .querySelector('[href="#' + t + '"]')
              .classList.add("active"))
        : document.body.classList.add("twocolumn-panel"));
  }

  function L() {
    var e =
      "/" == location.pathname ? "index.html" : location.pathname.substring(1);
    (e = e.substring(e.lastIndexOf("/") + 1)) &&
      (e = document
        .getElementById("navbar-nav")
        .querySelector('[href="' + e + '"]')) &&
      (e.classList.add("active"), (e = e.closest(".collapse.menu-dropdown"))) &&
      (e.classList.add("show"),
      e.parentElement.children[0].classList.add("active"),
      e.parentElement.children[0].setAttribute("aria-expanded", "true"),
      e.parentElement.closest(".collapse.menu-dropdown")) &&
      (e.parentElement.closest(".collapse").classList.add("show"),
      e.parentElement.closest(".collapse").previousElementSibling &&
        e.parentElement
          .closest(".collapse")
          .previousElementSibling.classList.add("active"),
      e.parentElement.parentElement.parentElement.parentElement.closest(
        ".collapse.menu-dropdown"
      )) &&
      (e.parentElement.parentElement.parentElement.parentElement
        .closest(".collapse")
        .classList.add("show"),
      e.parentElement.parentElement.parentElement.parentElement.closest(
        ".collapse"
      ).previousElementSibling) &&
      (e.parentElement.parentElement.parentElement.parentElement
        .closest(".collapse")
        .previousElementSibling.classList.add("active"),
      "horizontal" ==
        document.documentElement.getAttribute("data-layout-default")) &&
      e.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.closest(
        ".collapse"
      ) &&
      e.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement.parentElement
        .closest(".collapse")
        .previousElementSibling.classList.add("active");
  }

  function v(e) {
    if (e) {
      var t = e.offsetTop,
        a = e.offsetLeft,
        o = e.offsetWidth,
        n = e.offsetHeight;
      if (e.offsetParent)
        for (; e.offsetParent; )
          (t += (e = e.offsetParent).offsetTop), (a += e.offsetLeft);
      return (
        t >= window.pageYOffset &&
        a >= window.pageXOffset &&
        t + n <= window.pageYOffset + window.innerHeight &&
        a + o <= window.pageXOffset + window.innerWidth
      );
    }
  }

  // CountDown Start
  function D() {
    var e = document.querySelectorAll(".counter-value");
    function s(e) {
      return e.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    e &&
      Array.from(e).forEach(function (n) {
        !(function e() {
          var t = +n.getAttribute("data-target"),
            a = +n.innerText,
            o = t / 250;
          o < 1 && (o = 1),
            a < t
              ? ((n.innerText = (a + o).toFixed(0)), setTimeout(e, 1))
              : (n.innerText = s(t)),
            s(n.innerText);
        })();
      });
  }
  // CountDown End

  function x() {
    setTimeout(function () {
      var e,
        t,
        a = document.getElementById("navbar-nav");
      a &&
        ((a = a.querySelector(".nav-item .active")),
        300 < (e = a ? a.offsetTop : 0)) &&
        (t = document.getElementsByClassName("app-menu")
          ? document.getElementsByClassName("app-menu")[0]
          : "") &&
        t.querySelector(".simplebar-content-wrapper") &&
        setTimeout(function () {
          t.querySelector(".simplebar-content-wrapper").scrollTop =
            330 == e ? e + 85 : e;
        }, 0);
    }, 250);
  }

  function N() {
    document.webkitIsFullScreen ||
      document.mozFullScreen ||
      document.msFullscreenElement ||
      document.body.classList.remove("fullscreen-enable");
  }

  function H() {
    Array.from(
      document.querySelectorAll("#notificationItemsTabContent .tab-pane")
    ).forEach(function (e) {
      0 < e.querySelectorAll(".notification-item").length
        ? e.querySelector(".view-all") &&
          (e.querySelector(".view-all").style.display = "block")
        : (e.querySelector(".view-all") &&
            (e.querySelector(".view-all").style.display = "none"),
          e.querySelector(".empty-notification-elem") ||
            (e.innerHTML +=
              '<div class="empty-notification-elem">\t\t\t\t\t\t\t<div class="w-25 w-sm-50 pt-3 mx-auto">\t\t\t\t\t\t\t\t<img src="assets/images/svg/bell.svg" class="img-fluid" alt="user-pic">\t\t\t\t\t\t\t</div>\t\t\t\t\t\t\t<div class="text-center pb-5 mt-2">\t\t\t\t\t\t\t\t<h6 class="fs-18 fw-semibold lh-base">Hey! You have no any notifications </h6>\t\t\t\t\t\t\t</div>\t\t\t\t\t\t</div>'));
    });
  }

  (o = document.getElementById("search-close-options")),
    (s = document.getElementById("search-dropdown")),
    (d = document.getElementById("search-options")) &&
      (d.addEventListener("focus", function () {
        0 < d.value.length
          ? (s.classList.add("show"), o.classList.remove("d-none"))
          : (s.classList.remove("show"), o.classList.add("d-none"));
      }),
      d.addEventListener("keyup", function (e) {
        var n, t;
        0 < d.value.length
          ? (s.classList.add("show"),
            o.classList.remove("d-none"),
            (n = d.value.toLowerCase()),
            (t = document.getElementsByClassName("notify-item")),
            Array.from(t).forEach(function (e) {
              var t,
                a,
                o = "";
              e.querySelector("h6")
                ? ((t = e
                    .getElementsByTagName("span")[0]
                    .innerText.toLowerCase()),
                  (o = (a = e
                    .querySelector("h6")
                    .innerText.toLowerCase()).includes(n)
                    ? a
                    : t))
                : e.getElementsByTagName("span") &&
                  (o = e
                    .getElementsByTagName("span")[0]
                    .innerText.toLowerCase()),
                o && (e.style.display = o.includes(n) ? "block" : "none");
            }))
          : (s.classList.remove("show"), o.classList.add("d-none"));
      }),
      o.addEventListener("click", function () {
        (d.value = ""), s.classList.remove("show"), o.classList.add("d-none");
      }),
      document.body.addEventListener("click", function (e) {
        "search-options" !== e.target.getAttribute("id") &&
          (s.classList.remove("show"), o.classList.add("d-none"));
      })),
    (t = document.getElementById("search-close-options")),
    (n = document.getElementById("search-dropdown-reponsive")),
    (e = document.getElementById("search-options-reponsive")),
    t &&
      n &&
      e &&
      (e.addEventListener("focus", function () {
        0 < e.value.length
          ? (n.classList.add("show"), t.classList.remove("d-none"))
          : (n.classList.remove("show"), t.classList.add("d-none"));
      }),
      e.addEventListener("keyup", function () {
        0 < e.value.length
          ? (n.classList.add("show"), t.classList.remove("d-none"))
          : (n.classList.remove("show"), t.classList.add("d-none"));
      }),
      t.addEventListener("click", function () {
        (e.value = ""), n.classList.remove("show"), t.classList.add("d-none");
      }),
      document.body.addEventListener("click", function (e) {

        "search-options" !== e.target.getAttribute("id") &&
          (n.classList.remove("show"), t.classList.add("d-none"));
      })),
    // Full-Screen Moad Start
    (i = document.querySelector('[data-toggle="fullscreen"]')) &&
      i.addEventListener("click", function (e) {
        e.preventDefault(),
          document.body.classList.toggle("fullscreen-enable"),
          document.fullscreenElement ||
          document.mozFullScreenElement ||
          document.webkitFullscreenElement
            ? document.cancelFullScreen
              ? document.cancelFullScreen()
              : document.mozCancelFullScreen
              ? document.mozCancelFullScreen()
              : document.webkitCancelFullScreen &&
                document.webkitCancelFullScreen()
            : document.documentElement.requestFullscreen
            ? document.documentElement.requestFullscreen()
            : document.documentElement.mozRequestFullScreen
            ? document.documentElement.mozRequestFullScreen()
            : document.documentElement.webkitRequestFullscreen &&
              document.documentElement.webkitRequestFullscreen(
                Element.ALLOW_KEYBOARD_INPUT
              );
      }),
    document.addEventListener("fullscreenchange", N),
    document.addEventListener("webkitfullscreenchange", N),
    document.addEventListener("mozfullscreenchange", N),
    // Full-Screen Moad End
    O(),
    D(),
    [].slice
      .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .map((e) => {
        return new bootstrap.Tooltip(e);
      }),
    [].slice
      .call(document.querySelectorAll('[data-bs-toggle="popover"]'))
      .map((e) => {
        return new bootstrap.Popover(e);
      }),
    // Choices Select Start
    (m = document.querySelectorAll("[data-choices]")),
    Array.from(m).forEach(function (e) {
      var t = {},
        a = e.attributes;
      a["data-choices-groups"] &&
        (t.placeholderValue = "This is a placeholder set in the config"),
        a["data-choices-search-false"] && (t.searchEnabled = !1),
        a["data-choices-search-true"] && (t.searchEnabled = !0),
        a["data-choices-removeItem"] && (t.removeItemButton = !0),
        a["data-choices-sorting-false"] && (t.shouldSort = !1),
        a["data-choices-sorting-true"] && (t.shouldSort = !0),
        a["data-choices-multiple-remove"] && (t.removeItemButton = !0),
        a["data-choices-limit"] &&
          (t.maxItemCount = a["data-choices-limit"].value.toString()),
        a["data-choices-limit"] &&
          (t.maxItemCount = a["data-choices-limit"].value.toString()),
        a["data-choices-editItem-true"] && (t.maxItemCount = !0),
        a["data-choices-editItem-false"] && (t.maxItemCount = !1),
        a["data-choices-text-unique-true"] && (t.duplicateItemsAllowed = !1),
        a["data-choices-text-disabled-true"] && (t.addItems = !1),
        a["data-choices-text-disabled-true"]
          ? new Choices(e, t).disable()
          : new Choices(e, t);
    }),
    // Choices Select End

    Array.from(
      document.querySelectorAll('.dropdown-menu a[data-bs-toggle="tab"]')
    ).forEach(function (e) {
      e.addEventListener("click", function (e) {
        e.stopPropagation(), bootstrap.Tab.getInstance(e.target).show();
      });
    }),
    f(),
    x(),
    window.addEventListener("resize", function () {
      c && clearTimeout(c), (c = setTimeout(2e3));
    });
})();

// BackToTop Btn Start
var mybutton = document.getElementById("back-to-top");
function scrollFunction() {
  100 < document.body.scrollTop || 100 < document.documentElement.scrollTop
    ? (mybutton.style.display = "block")
    : (mybutton.style.display = "none");
}
function topFunction() {
  (document.body.scrollTop = 0), (document.documentElement.scrollTop = 0);
}
mybutton &&
  (window.onscroll = function () {
    scrollFunction();
  });


let div = document.createElement("div");
div.classList.add("ripple");
const wavesEffect = Array.from(document.querySelectorAll(".waves"));
wavesEffect &&
  wavesEffect.forEach((wavesEffect) => {
    let timerId;
    wavesEffect.addEventListener("mousedown", (e) => {
      wavesEffect.append(div);
      clearTimeout(timerId);
      const size = wavesEffect.offsetWidth;
      const pos = wavesEffect.getBoundingClientRect();
      const x = e.pageX - pos.left - size;
      const y = e.pageY - pos.top - size;
      const ripple = e.target.querySelector(".ripple");
      if (ripple) {
        ripple.style =
          "top:" +
          y +
          "px; left:" +
          x +
          "px; width: " +
          size * 2 +
          "px; height: " +
          size * 2 +
          "px;";
        if (ripple.classList) {
          ripple.classList.remove("button-click");
          ripple.classList.remove("start");
          setTimeout(() => {
            ripple.classList.add("start");
            setTimeout(() => {
              ripple.classList.add("button-click");
            });
          });
        }
      }
    });

    wavesEffect.addEventListener("mouseup", (e) => {
      const ripple = e.target.querySelector(".ripple");
      if (ripple != null) {
        clearTimeout(timerId);
        timerId = setTimeout(() => {
          ripple.classList.remove("button-click");
          ripple.classList.remove("start");
        }, 500);
      }
    });
  });


var myBtn = document.querySelectorAll(".nav-link");

for (var i = 0; i < myBtn.length; i++) {
  var btn = myBtn[i];
  btn.addEventListener("click", function (event) {
    var self = this;
    var rect = this.getBoundingClientRect();
    var diameter = Math.max(rect.width, rect.height);

    var span = this.querySelector(".sidebar-wave");
    if (!span) {
      span = document.createElement("span");
      span.className = "sidebar-wave";
      span.style.height = diameter + "px";
      span.style.width = diameter + "px";
      this.appendChild(span);
    }
    span.classList.remove("anim");
    span.style.left = event.clientX - rect.left - diameter / 2 + "px";
    span.style.top = event.clientY - rect.top - diameter / 2 + "px";
    setTimeout(function () {
      span.classList.add("anim");
    }, 0);
  });
}


const sectionListBtn = document.querySelector(".section-list-btn")
sectionListBtn && sectionListBtn.addEventListener("click", () => {
  const sectionWrapper = document.querySelector(".section-list-wrapper")
  const nodata = document.querySelector(".no-data-found")
  sectionWrapper.classList.toggle("is-open")
  if(nodata){
    nodata.classList.toggle("d-none")
  }
 

  const navLinks = sectionWrapper.querySelectorAll(".nav-link")
  if (navLinks) {
    navLinks.forEach((item) => {
      item.addEventListener("click", () => {
        if (!sectionWrapper.classList.contains("is-open")) {
          sectionWrapper.classList.add("is-open");
        }
      })
    })
  }

})