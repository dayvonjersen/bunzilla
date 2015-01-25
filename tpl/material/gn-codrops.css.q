/**
 * Google Nexus Website Menu v1.0.0
 *
 * http://tympanus.net/codrops/?p=16030
 * 
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Copyright 2013, Codrops
 * http://www.codrops.com
 */

/**
 * @tsonotes
 * codrops example is based off of this:
 * http://web.archive.org/web/20130731035203/http://www.google.com/nexus/
 *
 * some modifications made to this CSS include:
 *  - translate3d() instead of translateX()
 *  - cubic-bezier() added to transition
 *
 *  - removing background, color, font, 
 *    and other styles to be more consistent
 *    with bunzilla and materializecss 
 *  - removing webfont icons */

.gn-menu-main, .gn-menu-main ul, .gn-scroller {
    background: #fff;
}

.gn-menu-main,
.gn-menu-main ul {
		margin: 0;
		padding: 0;
/*		background: white;
		color: #5f6f81;*/
		list-style: none;
		text-transform: none;
		font-weight: 300;
/*		font-family: 'Lato', Arial, sans-serif;
		line-height: 60px; */
}

.gn-menu-main {
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 60px;
		font-size: 13px;
}

.gn-menu-main a {
		display: block;
		height: 100%;
/*		color: #5f6f81; */
		text-decoration: none;
		cursor: pointer;
}

.no-touch .gn-menu-main a:hover,
.no-touch .gn-menu li.gn-search-item:hover,
.no-touch .gn-menu li.gn-search-item:hover a {
/*		background: #5f6f81;
		color: white; */
}

.gn-menu-main > li {
		display: block;
		float: left;
		height: 100%;
		border-right: 1px solid #c6d0da;
		text-align: center;
}

/* icon-only trigger (menu item) */

.gn-menu-main li.gn-trigger {
		position: relative;
		width: 60px;
		-webkit-touch-callout: none;
		-webkit-user-select: none;
		-khtml-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
}

.gn-menu-main > li:last-child {
		float: right;
		border-right: none;
		border-left: 1px solid #c6d0da;
}

.gn-menu-main > li > a {
		padding: 0 30px;
		text-transform: uppercase;
		letter-spacing: 1px;
		font-weight: bold;
}

.gn-menu-main:after {
		display: table;
		clear: both;
		content: "";
}

.gn-menu-wrapper {
		position: fixed;
		top: 60px;
		bottom: 0;
		left: 0;
		overflow: hidden;
		width: 60px;
		border-top: 1px solid #c6d0da;
		border-right: 1px solid #c6d0da;
        z-index: 10;
/*		background: white; */
		-webkit-transform: translate3d(-60px,0px,0px);
		-moz-transform: translate3d(-60px,0px,0px);
		transform: translate3d(-60px,0px,0px);
		-webkit-transition: -webkit-transform 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;, width 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
		-moz-transition: -moz-transform 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s, width 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
		transition: transform 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s, width 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
}

.gn-scroller {
		position: absolute;
		overflow-y: scroll;
		width: 370px;
		height: 100%;
}

.gn-menu {
		border-bottom: 1px solid #c6d0da;
		text-align: left;
		font-size: 18px;
}

.gn-menu li:not(:first-child),
.gn-menu li li {
		box-shadow: inset 0 1px #c6d0da
}

.gn-submenu li {
		overflow: hidden;
		height: 0;
		-webkit-transition: height 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
		-moz-transition: height 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
		transition: height 0.3s cubic-bezier(0.8, 0, 0.2, 1) 0s;
}

.gn-submenu li a {
		color: #c1c9d1
}

input[type='text'].gn-search {
		position: relative;
		z-index: 10;
		padding-left: 60px;
		outline: none;
		border: none;
		background: transparent;
		color: #5f6f81;
		font-weight: 300;
/*		font-family: 'Lato', Arial, sans-serif; */
		cursor: pointer;
        width: auto;
        display: inline-block;
}

/* placeholder */

.gn-search::-webkit-input-placeholder {
		color: #5f6f81
}

.gn-search:-moz-placeholder {
		color: #5f6f81
}

.gn-search::-moz-placeholder {
		color: #5f6f81
}

.gn-search:-ms-input-placeholder {
		color: #5f6f81
}

/* hide placeholder when active in Chrome */

.gn-search:focus::-webkit-input-placeholder,
.no-touch .gn-menu li.gn-search-item:hover .gn-search:focus::-webkit-input-placeholder {
		color: transparent
}

input.gn-search:focus {
		cursor: text
}

.no-touch .gn-menu li.gn-search-item:hover input.gn-search {
		color: white
}

/* placeholder */

.no-touch .gn-menu li.gn-search-item:hover .gn-search::-webkit-input-placeholder {
		color: white
}

.no-touch .gn-menu li.gn-search-item:hover .gn-search:-moz-placeholder {
		color: white
}

.no-touch .gn-menu li.gn-search-item:hover .gn-search::-moz-placeholder {
		color: white
}

.no-touch .gn-menu li.gn-search-item:hover .gn-search:-ms-input-placeholder {
		color: white
}

.gn-menu-main a.gn-icon-search {
		position: absolute;
		top: 0;
		left: 0;
		height: 60px;
}

.gn-icon::before {
		display: inline-block;
		width: 60px;
		text-align: center;
		text-transform: none;
		font-weight: normal;
		font-style: normal;
		font-variant: normal;
/*		font-family: 'ecoicons'; */
		line-height: 1;
		speak: none;
		-webkit-font-smoothing: antialiased;
}

/* if an icon anchor has a span, hide the span */

.gn-icon span {
		width: 0;
		height: 0;
		display: block;
		overflow: hidden;
}

.gn-icon-menu::before {
		margin-left: -15px;
		vertical-align: -2px;
		width: 30px;
		height: 3px;
		background: #5f6f81;
		box-shadow: 0 3px white, 0 -6px #5f6f81, 0 -9px white, 0 -12px #5f6f81;
		content: '';
}

.no-touch .gn-icon-menu:hover::before,
.no-touch .gn-icon-menu.gn-selected:hover::before {
		background: white;
		box-shadow: 0 3px #5f6f81, 0 -6px white, 0 -9px #5f6f81, 0 -12px white;
}

.gn-icon-menu.gn-selected::before {
		background: #5993cd;
		box-shadow: 0 3px white, 0 -6px #5993cd, 0 -9px white, 0 -12px #5993cd;
}

/* styles for opening menu */

.gn-menu-wrapper.gn-open-all,
.gn-menu-wrapper.gn-open-part {
		-webkit-transform: translate3d(0px,0px,0px);
		-moz-transform: translate3d(0px,0px,0px);
		transform: translate3d(0px,0px,0px);
}

.gn-menu-wrapper.gn-open-all {
		width: 340px
}

.gn-menu-wrapper.gn-open-all .gn-submenu li {
		height: 60px
}

@media screen and (max-width: 422px) { 
	.gn-menu-wrapper.gn-open-all {
			-webkit-transform: translate3d(0px,0px,0px);
			-moz-transform: translate3d(0px,0px,0px);
			transform: translate3d(0px,0px,0px);
			width: 100%;
	}

	.gn-menu-wrapper.gn-open-all .gn-scroller {
			width: 130%
	}
}
