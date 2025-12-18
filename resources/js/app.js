// Simple test without any imports
console.log("App.js loaded successfully");

// Global Variables
window.ASI = {
  config: {
    baseUrl: window.location.origin,
    csrfToken:
      document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "",
    locale: "id",
  },
  utils: {},
  components: {},
  tables: {},
};

// Document Ready
$(document).ready(function () {
  // Initialize all components
  ASI.init();
});

/**
 * Main initialization function
 */
ASI.init = function () {
  this.initSidebar();
  this.initTooltips();
  this.initAlerts();
  this.initForms();
  this.initModals();
  this.initNumberFormat();
  this.initDateFormat();
};

/**
 * Sidebar functionality
 */
ASI.initSidebar = function () {
  // Toggle sidebar
  $(document).on("click", ".sidebar-toggle", function (e) {
    e.preventDefault();
    $(".sidebar").toggleClass("collapsed");
    $(".main-content").toggleClass("expanded");

    // Save state to localStorage
    const isCollapsed = $(".sidebar").hasClass("collapsed");
    localStorage.setItem("sidebar_collapsed", isCollapsed);
  });

  // Restore sidebar state
  const sidebarCollapsed = localStorage.getItem("sidebar_collapsed");
  if (sidebarCollapsed === "true") {
    $(".sidebar").addClass("collapsed");
    $(".main-content").addClass("expanded");
  }

  // Mobile sidebar toggle
  $(document).on("click", ".mobile-sidebar-toggle", function (e) {
    e.preventDefault();
    $(".sidebar").toggleClass("show");
  });

  // Close sidebar on mobile when clicking outside
  $(document).on("click", function (e) {
    if ($(window).width() <= 768) {
      if (!$(e.target).closest(".sidebar, .mobile-sidebar-toggle").length) {
        $(".sidebar").removeClass("show");
      }
    }
  });
};

/**
 * Initialize tooltips
 */
ASI.initTooltips = function () {
  // Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
};

/**
 * Initialize alerts
 */
ASI.initAlerts = function () {
  // Auto hide alerts after 5 seconds
  $(".alert:not(.alert-permanent)").each(function () {
    const alert = $(this);
    setTimeout(function () {
      alert.fadeOut("slow");
    }, 5000);
  });
};

/**
 * Initialize forms
 */
ASI.initForms = function () {
  // Form validation styling
  $("form").on("submit", function () {
    $(this)
      .find('.btn[type="submit"]')
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
  });

  // Reset form validation on input change
  $(".form-control, .form-select").on("input change", function () {
    $(this).removeClass("is-invalid is-valid");
    $(this).siblings(".invalid-feedback").hide();
  });

  // Auto-format currency inputs
  $(document).on("input", ".currency-input", function () {
    ASI.utils.formatCurrency(this);
  });

  // Auto-format number inputs
  $(document).on("input", ".number-input", function () {
    ASI.utils.formatNumber(this);
  });
};

/**
 * Initialize modals
 */
ASI.initModals = function () {
  // Reset modal forms when closed
  $(".modal").on("hidden.bs.modal", function () {
    $(this).find("form")[0]?.reset();
    $(this)
      .find(".form-control, .form-select")
      .removeClass("is-invalid is-valid");
    $(this).find(".invalid-feedback").hide();
  });
};

/**
 * Initialize number formatting
 */
ASI.initNumberFormat = function () {
  // Format existing numbers on page load
  $(".format-number").each(function () {
    const value = $(this).text() || $(this).val();
    if (value && !isNaN(value)) {
      const formatted = ASI.utils.numberFormat(parseFloat(value));
      if ($(this).is("input")) {
        $(this).val(formatted);
      } else {
        $(this).text(formatted);
      }
    }
  });
};

/**
 * Initialize date formatting
 */
ASI.initDateFormat = function () {
  // Format existing dates on page load
  $(".format-date").each(function () {
    const value = $(this).text() || $(this).val();
    if (value) {
      const formatted = ASI.utils.dateFormat(value);
      if ($(this).is("input")) {
        $(this).val(formatted);
      } else {
        $(this).text(formatted);
      }
    }
  });
};

/**
 * Utility Functions
 */
ASI.utils = {
  /**
   * Format number with thousand separators
   */
  numberFormat: function (number, decimals = 0) {
    if (isNaN(number)) return "0";
    return new Intl.NumberFormat("id-ID", {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals,
    }).format(number);
  },

  /**
   * Format currency (IDR)
   */
  currencyFormat: function (number) {
    if (isNaN(number)) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(number);
  },

  /**
   * Format currency input field
   */
  formatCurrency: function (input) {
    let value = input.value.replace(/[^\d]/g, "");
    if (value) {
      input.value = this.numberFormat(parseInt(value));
    }
  },

  /**
   * Format number input field
   */
  formatNumber: function (input) {
    let value = input.value.replace(/[^\d.,]/g, "");
    if (value) {
      // Handle decimal separator
      const parts = value.split(/[.,]/);
      if (parts.length > 1) {
        const integer = parts[0];
        const decimal = parts[1].substring(0, 2); // Max 2 decimal places
        input.value = this.numberFormat(parseInt(integer)) + "," + decimal;
      } else {
        input.value = this.numberFormat(parseInt(value));
      }
    }
  },

  /**
   * Parse formatted number back to float
   */
  parseNumber: function (formattedNumber) {
    if (!formattedNumber) return 0;
    return (
      parseFloat(
        formattedNumber
          .toString()
          .replace(/[^\d,-]/g, "")
          .replace(",", ".")
      ) || 0
    );
  },

  /**
   * Format date to Indonesian format
   */
  dateFormat: function (date, format = "dd MMMM yyyy") {
    if (!date) return "";

    const d = new Date(date);
    if (isNaN(d.getTime())) return date;

    const months = [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ];

    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();

    return `${day} ${month} ${year}`;
  },

  /**
   * Show loading state
   */
  showLoading: function (element, text = "Memuat...") {
    const $el = $(element);
    $el.prop("disabled", true);
    $el.data("original-text", $el.html());
    $el.html(`<i class="fas fa-spinner fa-spin"></i> ${text}`);
  },

  /**
   * Hide loading state
   */
  hideLoading: function (element) {
    const $el = $(element);
    $el.prop("disabled", false);
    $el.html($el.data("original-text") || "Submit");
  },

  /**
   * Show toast notification
   */
  toast: function (message, type = "success", duration = 3000) {
    // Create toast container if not exists
    if (!$("#toast-container").length) {
      $("body").append(
        '<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>'
      );
    }

    const toastId = "toast-" + Date.now();
    const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

    $("#toast-container").append(toastHtml);
    const toast = new bootstrap.Toast(document.getElementById(toastId), {
      delay: duration,
    });
    toast.show();

    // Remove toast element after it's hidden
    $(`#${toastId}`).on("hidden.bs.toast", function () {
      $(this).remove();
    });
  },

  /**
   * Confirm dialog
   */
  confirm: function (message, callback, title = "Konfirmasi") {
    if (confirm(`${title}\n\n${message}`)) {
      if (typeof callback === "function") {
        callback();
      }
    }
  },

  /**
   * AJAX helper
   */
  ajax: function (options) {
    const defaults = {
      headers: {
        "X-CSRF-TOKEN": ASI.config.csrfToken,
        Accept: "application/json",
      },
      error: function (xhr) {
        let message = "Terjadi kesalahan pada server";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        ASI.utils.toast(message, "danger");
      },
    };

    return $.ajax($.extend(true, defaults, options));
  },
};

/**
 * DataTable Components
 */
ASI.tables = {
  /**
   * Initialize DataTable with default settings
   */
  init: function (selector, options = {}) {
    const defaults = {
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json",
      },
      responsive: true,
      pageLength: 25,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Semua"],
      ],
      dom:
        '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
        '<"row"<"col-sm-12"tr>>' +
        '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
      processing: true,
      serverSide: false,
      order: [[0, "asc"]],
      columnDefs: [
        {
          targets: "no-sort",
          orderable: false,
        },
        {
          targets: "text-center",
          className: "text-center",
        },
        {
          targets: "text-right",
          className: "text-end",
        },
      ],
    };

    return $(selector).DataTable($.extend(true, defaults, options));
  },

  /**
   * Refresh DataTable
   */
  refresh: function (table) {
    if (table && typeof table.ajax === "object") {
      table.ajax.reload(null, false);
    } else if (table) {
      table.draw();
    }
  },
};

/**
 * Form Components
 */
ASI.components.form = {
  /**
   * Validate form
   */
  validate: function (form) {
    let isValid = true;

    $(form)
      .find("[required]")
      .each(function () {
        const $field = $(this);
        const value = $field.val().trim();

        if (!value) {
          $field.addClass("is-invalid");
          isValid = false;
        } else {
          $field.removeClass("is-invalid").addClass("is-valid");
        }
      });

    return isValid;
  },

  /**
   * Reset form validation
   */
  resetValidation: function (form) {
    $(form)
      .find(".form-control, .form-select")
      .removeClass("is-invalid is-valid");
    $(form).find(".invalid-feedback").hide();
  },

  /**
   * Show field error
   */
  showError: function (field, message) {
    const $field = $(field);
    $field.addClass("is-invalid");

    let $feedback = $field.siblings(".invalid-feedback");
    if (!$feedback.length) {
      $feedback = $('<div class="invalid-feedback"></div>');
      $field.after($feedback);
    }

    $feedback.text(message).show();
  },
};

/**
 * Export functions to global scope for backward compatibility
 */
window.showLoading = ASI.utils.showLoading;
window.hideLoading = ASI.utils.hideLoading;
window.showToast = ASI.utils.toast;
window.formatCurrency = ASI.utils.currencyFormat;
window.formatNumber = ASI.utils.numberFormat;
window.parseNumber = ASI.utils.parseNumber;
window.formatDate = ASI.utils.dateFormat;

// Console log for debugging
if (typeof console !== "undefined") {
  console.log("ASI System JavaScript loaded successfully");
  console.log("Available functions:", Object.keys(ASI));
}
