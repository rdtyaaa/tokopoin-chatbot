(function () {

  "use strict";

  const preloaderWrapper = document.querySelector(".preloader-wrapper")
  if (preloaderWrapper) {

    function animaitedBG() {

      var Base, Particle, canvas, colors, context, draw, drawables, i, mouseX, mouseY, mouseVX, mouseVY, rand, update, click, min, max, colors, particles;

      min = 1;
      max = 8;
      particles = 200;
      colors = ["64, 32, 0", "228, 0, 70", "64, 0, 0", "200, 200, 200"];



      rand = function (a, b) {
        return Math.random() * (b - a) + a;
      };

      Particle = (function () {
        function Particle() {
          this.reset();
        }

        Particle.prototype.reset = function () {
          this.color = colors[~~(Math.random() * colors.length)];
          this.radius = rand(min, max);
          this.x = rand(0, canvas.width);
          this.y = rand(-20, canvas.height * 0.5);
          this.vx = -5 + Math.random() * 10;
          this.vy = 0.7 * this.radius;
          this.valpha = rand(0.02, 0.09);
          this.opacity = 0;
          this.life = 0;
          this.onupdate = undefined;
          this.type = "dust";
        };

        Particle.prototype.update = function () {
          this.x = (this.x + this.vx / 3);
          this.y = (this.y + this.vy / 3);

          if (this.opacity >= 1 && this.valpha > 0) this.valpha *= -1;
          this.opacity += this.valpha / 3;
          this.life += this.valpha / 3;

          if (this.type === "dust")
            this.opacity = Math.min(1, Math.max(0, this.opacity));
          else
            this.opacity = 1;

          if (this.onupdate) this.onupdate();
          if (this.life < 0 || this.radius <= 0 || this.y > canvas.height) {
            this.onupdate = undefined;
            this.reset();
          }
        };

        Particle.prototype.draw = function (c) {
          c.strokeStyle = "rgba(" + this.color + ", " + Math.min(this.opacity, 0.85) + ")";
          c.fillStyle = "rgba(" + this.color + ", " + Math.min(this.opacity, 0.65) + ")";
          c.beginPath();
          c.arc(this.x, this.y, this.radius, 2 * Math.PI, false);
          c.fill();
          c.stroke();
        };

        return Particle;

      })();

      mouseVX = mouseVY = mouseX = mouseY = 0;

      canvas = document.getElementById("bg");
      context = canvas.getContext("2d");
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;

      drawables = (function () {
        var _i, _results;
        _results = [];
        for (i = _i = 1; _i <= particles; i = ++_i) {
          _results.push(new Particle);
        }
        return _results;
      })();

      draw = function () {
        var d, _i, _len;
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        context.clearRect(0, 0, canvas.width, canvas.height)

        for (_i = 0, _len = drawables.length; _i < _len; _i++) {
          d = drawables[_i];
          d.draw(context);
        }
      };

      update = function () {
        var d, _i, _len, _results;
        _results = [];
        for (_i = 0, _len = drawables.length; _i < _len; _i++) {
          d = drawables[_i];
          _results.push(d.update());
        }
        return _results;
      };

      document.onmousemove = function (e) {
        mouseVX = mouseX;
        mouseVY = mouseY;
        mouseX = ~~e.pageX;
        mouseY = ~~e.pageY;
        mouseVX = ~~((mouseVX - mouseX) / 2);
        mouseVY = ~~((mouseVY - mouseY) / 2);

      };

      window.addEventListener('resize', draw, false);
      setInterval(draw, 1000 / 30);
      setInterval(update, 1000 / 60);
    };
    const animation = animaitedBG()
    window.addEventListener("load", () => {
      animation
      preloaderWrapper.remove();
    });

  }


  // Header Top
  const headerTop = document.querySelector(".header-top");
  if (headerTop != null) {
    const headerTopHide = headerTop.querySelector(".header-top-hide");
    headerTopHide.addEventListener("click", () => {
      headerTop.classList.add("header-hide");
    });
  }

  // Mobile Search bar
  let mobileSearch = document.querySelector(".mobile-search");
  let mobileSearchBtn = document.querySelector(".mobile-search-tigger");

  if (mobileSearch != null) {
    mobileSearchBtn.addEventListener("click", () => {
      mobileSearch.classList.add("showMobileSearch");
      if (mobileSearch.classList.contains("showMobileSearch")) {
        const close = mobileSearch.querySelector(".mobile-search-close");
        close.addEventListener("click", () => {
          mobileSearch.classList.remove("showMobileSearch");
          let l = document.querySelector(".live-search");
          if (l.style.display == "block") {
            l.style.display = "none";
          } else {
            l.style.display = "block";
          }
        });
      }
    });
  }

  $(document).on("click", function (event) {
    if (document.querySelector(".live-search")) {
      if (!$(event.target).closest(".live-search").length) {
        $(".live-search").hide();
      }
    }
  });

  // Side Bar
  var offcanvasElementList = [].slice.call(
    document.querySelectorAll(".offcanvas")
  );
  var offcanvasList = offcanvasElementList.map(function (offcanvasEl) {
    return new bootstrap.Offcanvas(offcanvasEl);
  });

  window.onload = () => {
    offcanvasElementList.map((offcanvasEl) => {
      offcanvasEl.style.display = "block";
    });
  };

  // Login tabs
  const loginOption = document.querySelector(".login-options");
  const loginBtn = document.querySelectorAll(".login-tab-btn");
  const loginForms = document.querySelectorAll(".login-form");
  if (loginBtn && loginBtn.length > 0) {
    Array.from(loginBtn).forEach((item, i) => {
      item.addEventListener("click", () => {
        if (item.classList.contains("forgot-pass")) {
          loginOption.classList.add("d-none");
        } else {
          loginOption.classList.remove("d-none");
        }

        if (item.classList.contains("back-login")) {
          loginOption.classList.remove("d-none");
          loginBtn[0].classList.add("login-option-active");
          if (loginForms[0].classList.contains("sign-in-form")) {
            loginForms[0].classList.add("show-form")
          }
        }

        document.querySelectorAll(".login-form") &&
          Array.from(document.querySelectorAll(".login-form")).forEach((ele) => {
            ele.classList.remove("show-form");
          });

        Array.from(loginBtn).forEach((item) => {
          item.classList.remove("login-option-active");
        });

        loginBtn[i].classList.add("login-option-active");

        if (loginForms && loginForms.length > i) {
          loginForms[i]?.classList.add("show-form");
        }
      });
    });
  }

  const backLogin = document.querySelector(".back-login")
  backLogin && backLogin.addEventListener("click", () => {
    const signInform = document.querySelector(".sign-in-form")
    const signintab = document.querySelector(".signintab")
    signintab.classList.add("login-option-active")
    signInform.classList.add("show-form");
    if (loginOption.classList.contains("d-none")) {
      loginOption.classList.remove("d-none");
    }
  })


  // Reppal wave effect
  var myBtn = document.querySelectorAll(".wave-btn");
  for (var i = 0; i < myBtn.length; i++) {
    var btn = myBtn[i];
    btn.addEventListener("click", function (event) {
      var self = this;
      var rect = this.getBoundingClientRect();
      var diameter = Math.max(rect.width, rect.height);

      var span = this.querySelector(".wave");
      if (!span) {
        span = document.createElement("span");
        span.className = "wave";
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

  // Custom Select  Dropdown
  document.querySelectorAll(".Dropdown").forEach(function (dropdownWrapper) {
    const dropdownBtn = dropdownWrapper.querySelector(".dropdown__button");
    const dropdownList = dropdownWrapper.querySelector(".dropdown__list");

    dropdownBtn.addEventListener("click", function () {
      dropdownList.classList.toggle("dropdown__list_visible");
      this.classList.toggle("dropdown__button_active");
    });

    document.addEventListener("click", function (e) {
      if (e.target !== dropdownBtn) {
        dropdownBtn.classList.remove("dropdown__button_active");
        dropdownList.classList.remove("dropdown__list_visible");
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Tab" || e.key === "Escape") {
        dropdownBtn.classList.remove("dropdown__button_active");
        dropdownList.classList.remove("dropdown__list_visible");
      }
    });
  });


  // Cookies
  const cookies = document.querySelector(".cookies");
  if (cookies != null) {
    const showCookies = () => {
      cookies.classList.add("showCookies");
      if (cookies.classList.contains("showCookies")) {
        const hideCookie = cookies.querySelectorAll("a");
        hideCookie.forEach((item) => {
          item.addEventListener("click", () => {
            cookies.classList.remove("showCookies");
          });
        });
      }
    };
    window.addEventListener("load", () => {
      setTimeout(() => {
        showCookies();
      }, 6000);
    });
  }

  function chechInput(selector, type = false) {
    var class_list = $(`.${selector}`);
    for (var i = 0; i < class_list.length; i++) {
      if (type == "radio") {
        if ($(`.shiping-info:checked`).length == 0) {
          return false;
          break;
        }
      } else {
        if ($(class_list[i]).val() == "") {
          return false;
          break;
        }
      }
    }
  }

  // Checkout page Steps
  var checked_class = null;
  var type = false;
  var CheckoutTab = document.querySelectorAll(".checkout-tab");
  CheckoutTab &&
    Array.from(document.querySelectorAll(".checkout-tab")).forEach(function (o) {
      o.querySelectorAll(".nexttab") &&
        Array.from(o.querySelectorAll(".nexttab")).forEach(function (t) {
          var e = o.querySelectorAll('button[data-bs-toggle="pill"]');
          e &&
            (Array.from(e).forEach(function (e) {
              e.addEventListener("show.bs.tab", function (e) {
                e.target.classList.add("done");
              });
            }),
              t.addEventListener("click", function () {
                if (t.classList.contains("check-input")) {
                  checked_class = "user-info";
                  type = false;
                }
                if (t.classList.contains("shiping-input")) {
                  checked_class = "shiping-info";
                  type = "radio";
                }
                var e = t.getAttribute("data-nexttab");
                if (chechInput(checked_class, type) == false) {
                  toaster("Please Fill ALL The Input Field !!", "danger");
                } else {
                  
                  e && document.getElementById(e).click();
                }
              }));
        });

      document.querySelectorAll(".previestab") &&
        Array.from(o.querySelectorAll(".previestab")).forEach(function (r) {
          r.addEventListener("click", function () {
            var e = r.getAttribute("data-previous");
            if (e) {
              var t = r
                .closest("form")
                .querySelectorAll(".custom-nav .done").length;
              if (t) {
                for (var o = t - 1; o < t; o++)
                  r.closest("form").querySelectorAll(".custom-nav .done")[o] &&
                    r
                      .closest("form")
                      .querySelectorAll(".custom-nav .done")
                    [o].classList.remove("done");
                document.getElementById(e).click();
              }
            }
          });
        });
      var r = o.querySelectorAll('button[data-bs-toggle="pill"]');
      r &&
        Array.from(r).forEach(function (e, t) {
          e.setAttribute("data-position", t),
            e.addEventListener("click", function () {
              0 < o.querySelectorAll(".custom-nav .done").length &&
                Array.from(o.querySelectorAll(".custom-nav .done")).forEach(
                  function (e) {
                    e.classList.remove("done");
                  }
                );
              for (var e = 0; e <= t; e++)
                r[e].classList.contains("active")
                  ? r[e].classList.remove("done")
                  : r[e].classList.add("done");
            });
        });
    });

  // Banner Slider
  const banner1 = document.querySelector(".banner-slider");
  banner1 && new Swiper(banner1, {
    slidesPerView: 1,
    loop: true,
    speed: 1200,
    effect: 'fade',
    fadeEffect: {
      crossFade: true
    },
    pagination: {
      el: ".banner-pagination",
      clickable: true
    },
  });

  // New Arrivals Slider
  const newArrivalSlider = document.querySelector(".new-arrival-slider");
  newArrivalSlider && new Swiper(newArrivalSlider, {
    slidesPerView: 5,
    spaceBetween: 20,
    navigation: {
      nextEl: ".newarrivals-next",
      prevEl: ".newarrivals-prev",
    },
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 10,
      },
      600: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 4,
      },
      1200: {
        slidesPerView: 5,
      },
    },
  });

  // Flash Slider
  const flashSlider = document.querySelector(".flash-slider");
  flashSlider && new Swiper(flashSlider, {
    slidesPerView: 1,
    spaceBetween: 20,
    speed: 800,
    navigation: {
      nextEl: ".flash-next",
      prevEl: ".flash-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      type: "dynamic",
    },
    loop: true,
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 10,
      },
      600: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 20,
      },
      1200: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
  });

  // Category Slider
  const categorySlider = document.querySelector(".category-slider");
  categorySlider && new Swiper(categorySlider, {
    spaceBetween: 20,
    centeredSlides: false,
    autoplay: {
      delay: 3000,
    },
    speed: 800,
    pagination: {
      el: ".swiper-pagination",
      clickable: true
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev"
    },
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 10,
      },
      600: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 5,
      },
      1200: {
        slidesPerView: 7,
      },
      1400: {
        slidesPerView: 8,
      },
    },
  });

  // Testimonial Slider
  const testimonialSlider = document.querySelector(".testimonial-slider");
  testimonialSlider && new Swiper(testimonialSlider, {
    slidesPerView: 1,
    spaceBetween: 20,
    speed: 800,
    pagination: false,
    loop: true,
    navigation: {
      nextEl: ".testimonial-next",
      prevEl: ".testimonial-prev",
    },
  });

  // Auth Slider
  const testimonialSlider2 = document.querySelector(".testi-two-slider");
  testimonialSlider2 && new Swiper(testimonialSlider2, {
    slidesPerView: 1,
    spaceBetween: 20,
    speed: 800,
    pagination: {
      el: ".pagination-one",
      clickable: true,
    },
    loop: true,
  });

  // Related-shop-slider Slider
  const relatedShop = document.querySelector(".related-shop-slider");
  relatedShop && new Swiper(relatedShop, {
    slidesPerView: 1,
    spaceBetween: 20,
    navigation: {
      nextEl: ".testimonial-next",
      prevEl: ".testimonial-prev",
    },
    loop: true,
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 10,
      },

      768: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 2,
      },
      1200: {
        slidesPerView: 3,
      },
    },
  });

  // SIDEBAR FILTER

  const filterButton = document.querySelector(".filter-btn");
  const filterSidebar = document.querySelector(".filter-sidebar");

  filterButton && filterButton.addEventListener('click', () => {
    filterSidebar.classList.toggle('show');
  })

  // Categories Dropdown
  const bannerContainer = document.querySelector(".banner-container");
  const menuButton = document.querySelector(".menu-category-btn");
  const dropDownIcon = document.querySelector(
    ".menu-category-btn .fa-chevron-down"
  );

  if (bannerContainer) {
    const floatingCategories = document.querySelector(".floting-categories .collapse");

    function updateMenuAttributes(scrollY) {
      const shouldShow = scrollY > 650;
      menuButton[shouldShow ? "setAttribute" : "removeAttribute"]("data-bs-toggle", "collapse");
      menuButton[shouldShow ? "setAttribute" : "removeAttribute"]("data-bs-target", "#categoryCollapse");
      menuButton[shouldShow ? "setAttribute" : "removeAttribute"]("aria-expanded", shouldShow ? "false" : null);
      menuButton[shouldShow ? "setAttribute" : "removeAttribute"]("aria-controls", shouldShow ? "categoryCollapse" : null);
      menuButton.classList.toggle("collapsed", shouldShow);
      dropDownIcon.style.display = shouldShow ? "block" : "none";

      if (!shouldShow && floatingCategories.classList.contains("show")) {
        floatingCategories.classList.remove("show");
      }
    }

    window.addEventListener("scroll", () => {
      updateMenuAttributes(window.scrollY);
    });

    updateMenuAttributes(window.scrollY);
  }

  const flotingCategories = document.querySelector(".floting-categories");
  if (flotingCategories) {
    window.addEventListener("click", (event) => {
      const collapse = flotingCategories.querySelector(".collapse");
      if (!flotingCategories.contains(event.target)) {
        const bsCollapse = bootstrap.Collapse.getInstance(document.getElementById("categoryCollapse"));
        if (bsCollapse) {
          bsCollapse.hide();
        }
      }
    });
  }

  function f() {
    var e;
    document.querySelectorAll(".mobilecategories-dropdown.collapse") &&
      ((e = document.querySelectorAll(".mobilecategories-dropdown.collapse")),
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
                for (var t = [], a = e.parentNode.firstChild; a;)
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
                      ((e = e.querySelectorAll(".browse-categories-item a")),
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
  };
  f()

}())