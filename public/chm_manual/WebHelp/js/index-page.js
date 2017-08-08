/*! HelpSmith Web Help System 2.2
* http://www.helpsmith.com
* Copyright (c) 2007-2016 Divcom Software */
$(document).ready(function(){var c=$("frame[name=topic]");if(1==c.length){var e=c.attr("src"),d=getTopicFromQuery(e),b="",a;""==d||!1===d?a="unknown.htm":(a=d,b=getQueryAnchor());if(e!=a||""!=b)""!=b&&(a+="#"+b),naviReady(function(){c.attr("src",a)})}});