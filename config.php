<?php

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
EOD;

$sql = "SELECT * FROM batch WHERE user_id = ? ORDER BY id DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

// Assuming there's a field 'name' in the 'batch' table to display as the menu title.
// If the field name is different, replace 'name' with the appropriate field name.
while ($row = $result->fetch_assoc()) {
    // use audio_files table original_names to create nice batch names
    // first get jobs in batch as they contain audio_files.id
    $sql2 = "SELECT * FROM jobs LEFT JOIN files ON files.id = jobs.model_id  WHERE jobs.batch_id = ?";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param('i', $row['id']);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $job = $result2->fetch_assoc();
    // count how many other files are in the batch
    $sql3 = "SELECT COUNT(*) FROM jobs WHERE batch_id = ?";
    $stmt3 = $con->prepare($sql3);
    $stmt3->bind_param('i', $row['id']);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $count = $result3->fetch_assoc();

    // now get audio_files.original_name and create batch name
    $sql4 = "SELECT * FROM audio_files WHERE id = ?";
    $stmt4 = $con->prepare($sql4);
    $stmt4->bind_param('i', $job['audio_id']);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $audio_file = $result4->fetch_assoc();
    $batch_name = $audio_file['original_name'];
    // shorten batch name if longer than 20 characters
    if (strlen($batch_name) > 20) {
        $batch_name = substr($batch_name, 0, 20) . '...';
    }
    // shorten jon['name'] if longer than 10 characters
    $jobname = $job['original_name'];
    if (strlen($jobname) > 10) {
        $jobname = substr($jobname, 0, 10) . '...';
    }
    // add pitch to jobname if not 0
    if ($job['pitch'] != 0) {
        $jobname .= ' ' . $job['pitch'];
    }
    if ($count['COUNT(*)'] > 1) {
        $batch_name .= ' (' . ($count['COUNT(*)'] - 1) . ' more)';
    }

    // if $row['status'] == "complete" $menucolor = green else yellow
    $menucolor = ' style="color: #FFC107;"';
    if ($row['status'] == 'complete') {
        $menucolor = ' style="color: #28C76F;"';
    }

    $menu2 .=  '<div class="menu-item">';
    $menu2 .=  '<a class="menu-link" href="/processed?batch=' . $row['id'] . '">';
    $menu2 .=  '<span class="menu-bullet">';
    $menu2 .=  '<span class="bullet bullet-dot"></span>';
    $menu2 .=  '</span>';
    $menu2 .=  '<span class="menu-title"' . $menucolor . '>' . htmlspecialchars($batch_name) . ' ' . $jobname . '</span>';
    $menu2 .=  '</a>';
    $menu2 .=  '</div>';
}

$stmt->close();

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
                                    <a href="https://discord.gg/GEzDY6cy" target="_blank" class="menu-link">
                                        <span class="menu-icon">
                                            <i class="bi bi-discord fs-3"></i> </span>
                                        <span class="menu-title">Discord</span>
                                    </a>
                                </div>
                            </div>
                        </div>

EOD;
