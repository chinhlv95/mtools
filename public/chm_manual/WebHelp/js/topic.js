/*! HelpSmith Web Help System 2.2
* http://www.helpsmith.com
* Copyright (c) 2007-2016 Divcom Software */
$(document).ready(function(){highlightSearchWords();syncToc(topicContext)});function syncToc(b,d){if(0==parent.window.frames.length){if("0"!==getParamValue("frames",get_getQuery())){var a="../"+getWHSettings("indexFile","index.htm")+"?context="+b,c=getQueryAnchor();""!=c&&(a+="#"+c);window.location=a}}else naviReady(function(){var a=parent.window.frames.navi;a&&a.setCurrentTopic(b)})};