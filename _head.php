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
  ! function(t, e) {
    var o, n, p, r;
    e.__SV || (window.posthog = e, e._i = [], e.init = function(i, s, a) {
      function g(t, e) {
        var o = e.split(".");
        2 == o.length && (t = t[o[0]], e = o[1]), t[e] = function() {
          t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
        }
      }(p = t.createElement("script")).type = "text/javascript", p.async = !0, p.src = s.api_host + "/static/array.js", (r = t.getElementsByTagName("script")[0]).parentNode.insertBefore(p, r);
      var u = e;
      for (void 0 !== a ? u = e[a] = [] : a = "posthog", u.people = u.people || [], u.toString = function(t) {
          var e = "posthog";
          return "posthog" !== a && (e += "." + a), t || (e += " (stub)"), e
        }, u.people.toString = function() {
          return u.toString(1) + ".people (stub)"
        }, o = "capture identify alias people.set people.set_once set_config register register_once unregister opt_out_capturing has_opted_out_capturing opt_in_capturing reset isFeatureEnabled onFeatureFlags getFeatureFlag getFeatureFlagPayload reloadFeatureFlags group updateEarlyAccessFeatureEnrollment getEarlyAccessFeatures getActiveMatchingSurveys getSurveys onSessionId".split(" "), n = 0; n < o.length; n++) g(u, o[n]);
      e._i.push([i, s, a])
    }, e.__SV = 1)
  }(document, window.posthog || []);
  posthog.init('phc_JlYngwf9jrzunCsA8AJEqNph7Hxg7xqQpqgWwLKaElf', {
    api_host: 'https://app.posthog.com'
  })
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