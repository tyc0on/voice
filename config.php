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
