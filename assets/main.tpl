window.ConveadSettings = new Object;
{if isset($visitor_uid)}
window.ConveadSettings.visitor_uid = '{$visitor_uid}';
window.ConveadSettings.visitor_info = new Object;
window.ConveadSettings.visitor_info.first_name = '{$visitor_info['first_name']}';
window.ConveadSettings.visitor_info.email = '{$visitor_info['email']}';
{/if}
window.ConveadSettings.app_key = '{$app_key}';

{literal}
(function(w,d,c){w[c]=w[c]|| function(){{/literal}
{literal}(w[c].q=w[c].q||[]).push(arguments){/literal}
{literal}};var ts = (+new Date()/86400000|0)*86400;var s = d.createElement('script');s.type = 'text/javascript';s.async = true;s.src = 'https://tracker.convead.io/widgets/'+ts+'/widget-'+window.ConveadSettings.app_key+'.js';var x = d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);})(window,document,'convead');{/literal}
