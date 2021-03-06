// getElementsByClassName
if( !document.getElementsByClassName )
	document.getElementsByClassName = function( classname ){
		var elArray = [];
		var tmp = document.getElementsByTagName( "*" );
		var regex = new RegExp( "(^|\\s)" + classname + "(\\s|$)" );
		for( var i = 0; i < tmp.length; i++ )
			if( regex.test( tmp[ i ].className ) )
				elArray.push( tmp[ i ] );
		
		return elArray;
	};

// dom ready
!function(name,definition){if(typeof module!="undefined"){module.exports=definition()}else{if(typeof define=="function"&&typeof define.amd=="object"){define(definition)}else{this[name]=definition()}}}("domready",function(ready){var fns=[],fn,f=false,doc=document,testEl=doc.documentElement,hack=testEl.doScroll,domContentLoaded="DOMContentLoaded",addEventListener="addEventListener",onreadystatechange="onreadystatechange",readyState="readyState",loadedRgx=hack?/^loaded|^c/:/^loaded|c/,loaded=loadedRgx.test(doc[readyState]);function flush(f){loaded=1;while(f=fns.shift()){f()}}doc[addEventListener]&&doc[addEventListener](domContentLoaded,fn=function(){doc.removeEventListener(domContentLoaded,fn,f);flush()},f);hack&&doc.attachEvent(onreadystatechange,fn=function(){if(/^c/.test(doc[readyState])){doc.detachEvent(onreadystatechange,fn);flush()}});return(ready=hack?function(fn){self!=top?loaded?fn():fns.push(fn):function(){try{testEl.doScroll("left")}catch(e){return setTimeout(function(){ready(fn)},50)}fn()}()}:function(fn){loaded?fn():fns.push(fn)})});

// row grid
var rowGrid=function(f,a){function v(a){for(var d=[a];(a=a.nextSibling)&&9!==a.nodeType;)1===a.nodeType&&d.push(a);return d}function n(a,d,g){var f=0,e=[];g=Array.prototype.slice.call(g||a.querySelectorAll(d.itemSelector));var q=g.length,l=getComputedStyle(a);a=Math.floor(a.getBoundingClientRect().width)-parseFloat(l.getPropertyValue("padding-left"))-parseFloat(l.getPropertyValue("padding-right"));for(var l=[],c,k,p,b=0;b<q;++b)(c=g[b].getElementsByTagName("img")[0])?((k=parseInt(c.getAttribute("width")))||c.setAttribute("width",k=c.offsetWidth),(p=parseInt(c.getAttribute("height")))||c.setAttribute("height",p=c.offsetHeight),l[b]={width:k,height:p}):(g.splice(b,1),--b,--q);q=g.length;for(c=0;c<q;++c){g[c].classList?(g[c].classList.remove(d.firstItemClass),g[c].classList.remove(d.lastRowClass)):g[c].className=g[c].className.replace(new RegExp("(^|\\b)"+d.firstItemClass+"|"+d.lastRowClass+"(\\b|$)","gi")," ");f+=l[c].width;e.push(g[c]);if(c===q-1)for(b=0;b<e.length;b++){0===b&&(e[b].className+=" "+d.lastRowClass);var h="width: "+l[c+parseInt(b)-e.length+1].width+"px;height: "+l[c+parseInt(b)-e.length+1].height+"px;";b<e.length-1&&(h+="margin-right:"+d.minMargin+"px");e[b].style.cssText=h}if(f+d.maxMargin*(e.length-1)>a||window.innerWidth<d.minWidth){k=f+d.maxMargin*(e.length-1)-a;b=e.length;(d.maxMargin-d.minMargin)*(b-1)<k?(p=d.minMargin,k-=(d.maxMargin-d.minMargin)*(b-1)):(p=d.maxMargin-k/(b-1),k=0);for(var t,n=null,r=0,b=0;b<e.length;b++){t=e[b];var h=l[c+parseInt(b)-e.length+1].width,m=h-h/f*k,n=n||Math.round(l[c+parseInt(b)-e.length+1].height*(m/h));.5<=r+1-m%1?(r-=m%1,m=Math.floor(m)):(r+=1-m%1,m=Math.ceil(m));h="width: "+m+"px;height: "+n+"px;";b<e.length-1&&(h+="margin-right: "+p+"px");t.style.cssText=h;0===b&&d.firstItemClass&&(t.className+=" "+d.firstItemClass)}e=[];f=0}}}if(null!==f&&void 0!==f)if("appended"===a){a=JSON.parse(f.getAttribute("data-row-grid"));var u=f.getElementsByClassName(a.lastRowClass)[0],u=v(u);n(f,a,u)}else a?(void 0===a.resize&&(a.resize=!0),void 0===a.minWidth&&(a.minWidth=0),void 0===a.lastRowClass&&(a.lastRowClass="last-row")):a=JSON.parse(f.getAttribute("data-row-grid")),n(f,a),f.setAttribute("data-row-grid",JSON.stringify(a)),a.resize&&window.addEventListener("resize",function(u){n(f,a)})};"object"===typeof exports&&(module.exports=rowGrid);

if( el_d_gallery.className.indexOf( 'flex' ) == -1 ){
	var items = document.getElementsByClassName( 'item' );
	for( var i = 0; i < items.length; i++ )
		items[ i ].setAttribute( 'style', '' );
	
	domready( function(){
		rowGrid( document.getElementById( 'diPictures' ), {
			itemSelector: '.item',
			firstItemClass: 'item-first',
			maxMargin: di_settings.margin,
			minMargin: di_settings.margin,
			resize: true
		} );
	} );
}
