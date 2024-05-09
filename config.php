<?php

$bodysettings = 'data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"';

//data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-minimize="on" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true"

$head = <<<'EOD'

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    // Monitor page view
    sendEvent('page_view', window.location.href, null, null);

    // Monitor click events on the document
    document.addEventListener('click', function(e) {
        let monitoredElement = getMonitoredElement(e.target);
        let eventType, elementId, monitorData;

        if (monitoredElement) {
            eventType = monitoredElement.getAttribute('monitor-event');
            monitorData = monitoredElement.getAttribute('monitor-data');
        }

        // If monitor-event attribute is not found, default to tagName+'_click'
        eventType = eventType || e.target.tagName.toLowerCase() + '_click';

        // If no monitor-data attribute is found, default to text content
        monitorData = monitorData || e.target.textContent.trim() || e.target.value || null;

        // Try to get an id either from monitoredElement or from the clicked element itself
        elementId = (monitoredElement && monitoredElement.id) || e.target.id || null;

        sendEvent(eventType, window.location.href, elementId, monitorData);
    });
});

// Recursive function to find the nearest ancestor (or self) with 'monitor-event' attribute
function getMonitoredElement(element) {
    if (element.getAttribute('monitor-event') || element.parentElement == null) {
        return element;
    } else {
        return getMonitoredElement(element.parentElement);
    }
}

function sendEvent(eventType, page, elementId, monitorData) {
    fetch('/monitor.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_type: eventType,
            page: page,
            element_id: elementId,
            monitor_data: monitorData
        }),
    });
}
</script>
<script>
    !function(t,e){var o,n,p,r;e.__SV||(window.posthog=e,e._i=[],e.init=function(i,s,a){function g(t,e){var o=e.split(".");2==o.length&&(t=t[o[0]],e=o[1]),t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}}(p=t.createElement("script")).type="text/javascript",p.async=!0,p.src=s.api_host+"/static/array.js",(r=t.getElementsByTagName("script")[0]).parentNode.insertBefore(p,r);var u=e;for(void 0!==a?u=e[a]=[]:a="posthog",u.people=u.people||[],u.toString=function(t){var e="posthog";return"posthog"!==a&&(e+="."+a),t||(e+=" (stub)"),e},u.people.toString=function(){return u.toString(1)+".people (stub)"},o="capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys onSessionId".split(" "),n=0;n<o.length;n++)g(u,o[n]);e._i.push([i,s,a])},e.__SV=1)}(document,window.posthog||[]);
    posthog.init('phc_JlYngwf9jrzunCsA8AJEqNph7Hxg7xqQpqgWwLKaElf',{api_host:'https://app.posthog.com'})
</script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-N3K52G7P0P"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());

  gtag('config', 'G-N3K52G7P0P');
</script>
<script type="text/javascript">
  (function(c, l, a, r, i, t, y) {
    c[a] = c[a] || function() {
      (c[a].q = c[a].q || []).push(arguments)
    };
    t = l.createElement(r);
    t.async = 1;
    t.src = "https://www.clarity.ms/tag/" + i;
    y = l.getElementsByTagName(r)[0];
    y.parentNode.insertBefore(t, y);
  })(window, document, "clarity", "script", "i8dskjoul1");
</script>
EOD;












$menu2 = <<<EOD
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
<!--begin::Menu wrapper-->
<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
    <!--begin::Scroll wrapper-->
    <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">
                <!--begin:Menu link-->
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-element-11 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">History</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion">
                <div><a href="/all">Show All</a></div>
EOD;

// $sql = "SELECT * FROM batch WHERE user_id = ? ORDER BY id DESC";
// $stmt = $con->prepare($sql);
// $stmt->bind_param('i', $_SESSION['id']);
// $stmt->execute();
// $result = $stmt->get_result();

// while ($row = $result->fetch_assoc()) {
//   $sql2 = "SELECT * FROM jobs LEFT JOIN files ON files.id = jobs.model_id  WHERE jobs.batch_id = ?";
//   $stmt2 = $con->prepare($sql2);
//   $stmt2->bind_param('i', $row['id']);
//   $stmt2->execute();
//   $result2 = $stmt2->get_result();
//   $job = $result2->fetch_assoc();
//   $sql3 = "SELECT COUNT(*) FROM jobs WHERE batch_id = ?";
//   $stmt3 = $con->prepare($sql3);
//   $stmt3->bind_param('i', $row['id']);
//   $stmt3->execute();
//   $result3 = $stmt3->get_result();
//   $count = $result3->fetch_assoc();

//   $sql4 = "SELECT * FROM audio_files WHERE id = ?";
//   $stmt4 = $con->prepare($sql4);
//   $stmt4->bind_param('i', $job['audio_id']);
//   $stmt4->execute();
//   $result4 = $stmt4->get_result();
//   $audio_file = $result4->fetch_assoc();
//   $batch_name = $audio_file['original_name'];
//   if (strlen($batch_name) > 20) {
//     $batch_name = substr($batch_name, 0, 20) . '...';
//   }
//   $jobname = $job['original_name'];
//   if (strlen($jobname) > 10) {
//     $jobname = substr($jobname, 0, 10) . '...';
//   }
//   if ($job['pitch'] != 0) {
//     $jobname .= ' ' . $job['pitch'];
//   }
//   if ($count['COUNT(*)'] > 1) {
//     $batch_name .= ' (' . ($count['COUNT(*)'] - 1) . ' more)';
//   }

//   $menucolor = ' style="color: #FFC107;"';
//   if ($row['status'] == 'complete') {
//     $menucolor = ' style="color: #28C76F;"';
//   }

//   $menu2 .=  '<div class="menu-item">';
//   $menu2 .=  '<a class="menu-link ps-0 pb-0" href="/processed?batch=' . $row['id'] . '">';
//   $menu2 .=  '<span class="menu-bullet">';
//   $menu2 .=  '<span class="bullet bullet-dot"></span>';
//   $menu2 .=  '</span>';
//   $menu2 .=  '<span class="menu-title"' . $menucolor . '>' . htmlspecialchars($batch_name) . ' ' . $jobname . '</span>';
//   $menu2 .=  '</a>';
//   $menu2 .=  '</div>';
// }

// $stmt->close();

// if $_SESSION['id'] set to number
if (is_numeric($_SESSION['id'])) {

  //,COUNT(jobs.id) OVER (PARTITION BY batch.id) AS job_count

  $sql = "SELECT 
batch.id, 
batch.status,
jobs.pitch, 
audio_files.original_name,
files.original_name as file_original_name
FROM 
batch
LEFT JOIN 
jobs ON batch.id = jobs.batch_id
LEFT JOIN 
files ON files.id = jobs.model_id
LEFT JOIN 
audio_files ON audio_files.id = jobs.audio_id
WHERE 
batch.user_id = ?
ORDER BY 
batch.id DESC;";

  $stmt = $con->prepare($sql);
  $stmt->bind_param('i', $_SESSION['id']);
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $batch_name = $row['original_name'];
    if (@strlen($batch_name) > 20) {
      $batch_name = substr($batch_name, 0, 20) . '...';
    }

    $jobname = $row['file_original_name'];
    if (@strlen($jobname) > 19) {
      $jobname = substr($jobname, 0, 19) . '...';
    }
    if ($row['pitch'] != 0) {
      $jobname .= ' p' . $row['pitch'];
    }
    // if ($row['job_count'] > 1) {
    //   $batch_name =  $batch_name . ' +' . ($row['job_count'] - 1);
    // }

    $menucolor = ' style="color: #FFC107;"';
    if ($row['status'] == 'complete') {
      $menucolor = ' style="color: #28C76F;"';
    }

    $file_original_name = $row['file_original_name'];

    $menu2 .=  '<div class="menu-item">';
    $menu2 .=  '<a class="menu-link ps-0 pb-0" href="/processed?batch=' . $row['id'] . '">';
    $menu2 .=  '<span class="menu-bullet">';
    $menu2 .=  '<span class="bullet bullet-dot"></span>';
    $menu2 .=  '</span>';
    $menu2 .=  '<span class="menu-title"' . $menucolor . '>' . @htmlspecialchars($batch_name) . '<br>' . @htmlspecialchars($jobname) . '</span>';
    $menu2 .=  '</a>';
    $menu2 .=  '</div>';
  }

  $stmt->close();
}

$menu2 .= <<<EOD
</div>
<!--end:Menu sub-->
</div>

</div>
<!--end::Menu-->
</div>
<!--end::Scroll wrapper-->
</div>
<!--end::Menu wrapper-->
</div>
EOD;



$menu1 = <<<EOD
<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                            <!--begin::Menu-->
                            <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
                                <!-- <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2"><span class="menu-link"><span class="menu-title">Help</span><span class="menu-arrow d-lg-none"></span></span>
                                    <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px" style="">
                                        <div class="menu-item"><a class="menu-link" href="https://preview.keenthemes.com/html/metronic/docs/base/utilities" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right" data-bs-original-title="Check out over 200 in-house components, plugins and ready for use solutions" data-kt-initialized="1"><span class="menu-icon"><i class="ki-duotone ki-rocket fs-2"><span class="path1"></span><span class="path2"></span></i></span><span class="menu-title">Components</span></a></div>
                                    </div>
                                </div> -->
                                <div class="menu-item">
                                    <a href="https://voice-models.com" target="_blank" class="menu-link">
                                        <span class="menu-icon">
                                            <i class="bi bi-megaphone-fill fs-3"></i> </span>
                                        <span class="menu-title">Voice Models</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                                    <a href="https://discord.gg/czFGXsFJjh" target="_blank" class="menu-link">
                                        <span class="menu-icon">
                                            <i class="bi bi-discord fs-3"></i> </span>
                                        <span class="menu-title">Discord</span>
                                    </a>
                                </div>
                                <div class="menu-item">
                            <a href="/takedown" target="_blank" class="menu-link">
                                <span class="menu-icon">
                                    <i class="bi bi-c-square fs-3"></i>
                                </span>
                                <span class="menu-title">Takedown</span>
                            </a>
                        </div>
                            </div>
                        </div>

EOD;


$modals = '';

$scripts = <<<EOD
<script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
<script src="assets/plugins/custom/typedjs/typedjs.bundle.js"></script>
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="assets/js/widgets.bundle.js"></script>
<script src="assets/js/custom/widgets.js"></script>
<script src="assets/js/custom/apps/chat/chat.js"></script>
<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/type.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/budget.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/settings.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/team.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/targets.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/files.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/complete.js"></script>
<script src="assets/js/custom/utilities/modals/create-project/main.js"></script>
<script src="assets/js/custom/utilities/modals/new-target.js"></script>
<script src="assets/js/custom/utilities/modals/offer-a-deal/type.js"></script>
<script src="assets/js/custom/utilities/modals/offer-a-deal/details.js"></script>
<script src="assets/js/custom/utilities/modals/offer-a-deal/finance.js"></script>
<script src="assets/js/custom/utilities/modals/offer-a-deal/complete.js"></script>
<script src="assets/js/custom/utilities/modals/offer-a-deal/main.js"></script>
<script src="assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->

<script src="assets/js/custom/utilities/modals/create-app.js"></script>
<script>
"use strict";
var KTProjectOverview = (function () {
  var t = KTUtil.getCssVariableValue("--bs-primary"),
    e = KTUtil.getCssVariableValue("--bs-primary-light"),
    a = KTUtil.getCssVariableValue("--bs-success"),
    r = KTUtil.getCssVariableValue("--bs-success-light"),
    o = KTUtil.getCssVariableValue("--bs-gray-200"),
    n = KTUtil.getCssVariableValue("--bs-gray-500");
  return {
    init: function () {
      var s, i;
      !(function () {
        var t = document.getElementById("project_overview_chart");
        if (t) {
          var e = t.getContext("2d");
          new Chart(e, {
            type: "doughnut",
            data: {
              datasets: [
                {
                  data: [30, 45, 25],
                  backgroundColor: ["#00A3FF", "#50CD89", "#E4E6EF"],
                },
              ],
              labels: ["Active", "Completed", "Yet to start"],
            },
            options: {
              chart: { fontFamily: "inherit" },
              cutoutPercentage: 75,
              responsive: !0,
              maintainAspectRatio: !1,
              cutout: "75%",
              title: { display: !1 },
              animation: { animateScale: !0, animateRotate: !0 },
              tooltips: {
                enabled: !0,
                intersect: !1,
                mode: "nearest",
                bodySpacing: 5,
                yPadding: 10,
                xPadding: 10,
                caretPadding: 0,
                displayColors: !1,
                backgroundColor: "#20D489",
                titleFontColor: "#ffffff",
                cornerRadius: 4,
                footerSpacing: 0,
                titleSpacing: 0,
              },
              plugins: { legend: { display: !1 } },
            },
          });
        }
      })(),
        (s = document.getElementById("kt_project_overview_graph")),
        (i = parseInt(KTUtil.css(s, "height"))),
        s &&
          new ApexCharts(s, {
            series: [
              { name: "Incomplete", data: [70, 70, 80, 80, 75, 75, 75] },
              { name: "Complete", data: [55, 55, 60, 60, 55, 55, 60] },
            ],
            chart: { type: "area", height: i, toolbar: { show: !1 } },
            plotOptions: {},
            legend: { show: !1 },
            dataLabels: { enabled: !1 },
            fill: { type: "solid", opacity: 1 },
            stroke: { curve: "smooth", show: !0, width: 3, colors: [t, a] },
            xaxis: {
              categories: ["Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug"],
              axisBorder: { show: !1 },
              axisTicks: { show: !1 },
              labels: { style: { colors: n, fontSize: "12px" } },
              crosshairs: {
                position: "front",
                stroke: { color: t, width: 1, dashArray: 3 },
              },
              tooltip: {
                enabled: !0,
                formatter: void 0,
                offsetY: 0,
                style: { fontSize: "12px" },
              },
            },
            yaxis: { labels: { style: { colors: n, fontSize: "12px" } } },
            states: {
              normal: { filter: { type: "none", value: 0 } },
              hover: { filter: { type: "none", value: 0 } },
              active: {
                allowMultipleDataPointsSelection: !1,
                filter: { type: "none", value: 0 },
              },
            },
            tooltip: {
              style: { fontSize: "12px" },
              y: {
                formatter: function (t) {
                  return t + " tasks";
                },
              },
            },
            colors: [e, r],
            grid: {
              borderColor: o,
              strokeDashArray: 4,
              yaxis: { lines: { show: !0 } },
            },
            markers: { colors: [e, r], strokeColor: [t, a], strokeWidth: 3 },
          }).render(),
        (function () {
          var t = document.querySelector("#kt_profile_overview_table");
          if (!t) return;
          t.querySelectorAll("tbody tr").forEach((t) => {
            const e = t.querySelectorAll("td"),
              a = moment(e[1].innerHTML, "MMM D, YYYY").format();
            e[1].setAttribute("data-order", a);
          });
          const e = $(t).DataTable({ info: !1, order: [] }),
            a = document.getElementById("kt_filter_orders"),
            r = document.getElementById("kt_filter_year");
          var o, n;
          a.addEventListener("change", function (t) {
            e.column(3).search(t.target.value).draw();
          }),
            r.addEventListener("change", function (t) {
              switch (t.target.value) {
                case "thisyear":
                  (o = moment().startOf("year").format()),
                    (n = moment().endOf("year").format()),
                    e.draw();
                  break;
                case "thismonth":
                  (o = moment().startOf("month").format()),
                    (n = moment().endOf("month").format()),
                    e.draw();
                  break;
                case "lastmonth":
                  (o = moment()
                    .subtract(1, "months")
                    .startOf("month")
                    .format()),
                    (n = moment()
                      .subtract(1, "months")
                      .endOf("month")
                      .format()),
                    e.draw();
                  break;
                case "last90days":
                  (o = moment().subtract(30, "days").format()),
                    (n = moment().format()),
                    e.draw();
                  break;
                default:
                  (o = moment()
                    .subtract(100, "years")
                    .startOf("month")
                    .format()),
                    (n = moment().add(1, "months").endOf("month").format()),
                    e.draw();
              }
            }),
            $.fn.dataTable.ext.search.push(function (t, e, a) {
              var r = o,
                s = n,
                i = parseFloat(moment(e[1]).format()) || 0;
              return !!(
                (isNaN(r) && isNaN(s)) ||
                (isNaN(r) && i <= s) ||
                (r <= i && isNaN(s)) ||
                (r <= i && i <= s)
              );
            }),
            document
              .getElementById("kt_filter_search")
              .addEventListener("keyup", function (t) {
                e.search(t.target.value).draw();
              });
        })();
    },
  };
})();
KTUtil.onDOMContentLoaded(function () {
  KTProjectOverview.init();
});

</script>
<!-- <script src="assets/js/custom/dropzone.js"></script> -->
<!--end::Javascript-->
<script>
    var budgetSlider = document.querySelector("#kt_modal_create_campaign_budget_slider");
    var budgetValue = document.querySelector("#kt_modal_create_campaign_budget_label");

    noUiSlider.create(budgetSlider, {
        start: [0],
        connect: true,
        range: {
            "min": -25,
            "max": 25
        }
    });

    budgetSlider.noUiSlider.on("update", function(values, handle) {
        budgetValue.innerHTML = Math.round(values[handle]);
        if (handle) {
            budgetValue.innerHTML = Math.round(values[handle]);
        }
    });
</script>
EOD;
