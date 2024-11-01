// parse out our page assingments and see if the current page has an assigned group, if not default to 0
var pageAssignments = velaro_args.velaro_page_assingments ? JSON.parse(velaro_args.velaro_page_assingments) : {};
var group = pageAssignments['page_' + velaro_args.velaro_page_id] || 0;

// properties needed for velaro chat
Velaro.Globals.ActiveSite = velaro_args.velaro_siteID;
Velaro.Globals.ActiveGroup = group;
Velaro.Globals.InlineEnabled = true;
Velaro.Globals.VisitorMonitoringEnabled = velaro_args.velaro_vm_active == "1";
Velaro.Globals.InlinePosition = 0;