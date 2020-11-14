
<script src="https://wchat.freshchat.com/js/widget.js"></script>
<script>
    window.fcWidget.init({
        token: "c194bfa3-ac0b-4be8-a733-c1b2f1a129f3",
        host: "https://wchat.freshchat.com"
    });

  @if (\Illuminate\Support\Facades\Auth::check())

  window.fcWidget.setExternalId("{{ \Illuminate\Support\Facades\Auth::user()->email }}");
  window.fcWidget.user.setFirstName("{{ \Illuminate\Support\Facades\Auth::user()->firstname }}");
  window.fcWidget.user.setLastName("{{ \Illuminate\Support\Facades\Auth::user()->lastname }}");
  window.fcWidget.user.setEmail("{{ \Illuminate\Support\Facades\Auth::user()->email }}");
  window.fcWidget.user.setPhone("{{ \Illuminate\Support\Facades\Auth::user()->phone }}");

  window.fcWidget.user.setProperties({
    "Hub Partner": "{{ title_case($fc_hubPartner) }}",
    "Hub Package": "{{ title_case($fc_hubPackage) }}",
    "Business Name": "{{ title_case($fc_businessName) }}",
    "Business Type": "{{ title_case($fc_businessType) }}",
  });

  @endif


</script>