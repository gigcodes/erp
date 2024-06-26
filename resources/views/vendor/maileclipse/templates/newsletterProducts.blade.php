<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="x-ua-compatible" content="ie=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Lussolicious</title>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
      <style type="text/css">
         @font-face {
         font-family: icomoon;
         src: url("https://erp.amourint.com/newsletter/general/fonts/icomoon.woff") format("woff"), url("https://erp.amourint.com/newsletter/general/fonts/icomoon.woff2") format("woff2");
         font-weight: 400;
         font-style: normal;
         }
         /*! normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */
         /**
         * 1. Set default font family to sans-serif.
         * 2. Prevent iOS and IE text size adjust after device orientation change,
         *    without disabling user zoom.
         */
         html {
         font-family: sans-serif; /* 1 */
         -ms-text-size-adjust: 100%; /* 2 */
         -webkit-text-size-adjust: 100%; /* 2 */
         }
         /**
         * Remove default margin.
         */
         body {
         margin: 0;
         }
         /* HTML5 display definitions
         ========================================================================== */
         /**
         * Correct `block` display not defined for any HTML5 element in IE 8/9.
         * Correct `block` display not defined for `details` or `summary` in IE 10/11
         * and Firefox.
         * Correct `block` display not defined for `main` in IE 11.
         */
         article,
         aside,
         details,
         figcaption,
         figure,
         footer,
         header,
         hgroup,
         main,
         menu,
         nav,
         section,
         summary {
         display: block;
         }
         /**
         * 1. Correct `inline-block` display not defined in IE 8/9.
         * 2. Normalize vertical alignment of `progress` in Chrome, Firefox, and Opera.
         */
         audio,
         canvas,
         progress,
         video {
         display: inline-block; /* 1 */
         vertical-align: baseline; /* 2 */
         }
         /**
         * Prevent modern browsers from displaying `audio` without controls.
         * Remove excess height in iOS 5 devices.
         */
         audio:not([controls]) {
         display: none;
         height: 0;
         }
         /**
         * Address `[hidden]` styling not present in IE 8/9/10.
         * Hide the `template` element in IE 8/9/10/11, Safari, and Firefox < 22.
         */
         [hidden],
         template {
         display: none;
         }
         /* Links
         ========================================================================== */
         /**
         * Remove the gray background color from active links in IE 10.
         */
         a {
         background-color: transparent;
         }
         /**
         * Improve readability of focused elements when they are also in an
         * active/hover state.
         */
         a:active,
         a:hover {
         outline: 0;
         }
         /* Text-level semantics
         ========================================================================== */
         /**
         * Address styling not present in IE 8/9/10/11, Safari, and Chrome.
         */
         abbr[title] {
         border-bottom: 1px dotted;
         }
         /**
         * Address style set to `bolder` in Firefox 4+, Safari, and Chrome.
         */
         b,
         strong {
         font-weight: bold;
         }
         /**
         * Address styling not present in Safari and Chrome.
         */
         dfn {
         font-style: italic;
         }
         /**
         * Address variable `h1` font-size and margin within `section` and `article`
         * contexts in Firefox 4+, Safari, and Chrome.
         */
         h1 {
         font-size: 2em;
         margin: .67em 0;
         }
         /**
         * Address styling not present in IE 8/9.
         */
         mark {
         background: #ff0;
         color: #000;
         }
         /**
         * Address inconsistent and variable font size in all browsers.
         */
         small {
         font-size: 80%;
         }
         /**
         * Prevent `sub` and `sup` affecting `line-height` in all browsers.
         */
         sub,
         sup {
         font-size: 75%;
         line-height: 0;
         position: relative;
         vertical-align: baseline;
         }
         sup {
         top: -.5em;
         }
         sub {
         bottom: -.25em;
         }
         /* Embedded content
         ========================================================================== */
         /**
         * Remove border when inside `a` element in IE 8/9/10.
         */
         img {
         border: 0;
         }
         /**
         * Correct overflow not hidden in IE 9/10/11.
         */
         svg:not(:root) {
         overflow: hidden;
         }
         /* Grouping content
         ========================================================================== */
         /**
         * Address margin not present in IE 8/9 and Safari.
         */
         figure {
         margin: 1em 40px;
         }
         /**
         * Address differences between Firefox and other browsers.
         */
         hr {
         box-sizing: content-box;
         height: 0;
         }
         /**
         * Contain overflow in all browsers.
         */
         pre {
         overflow: auto;
         }
         /**
         * Address odd `em`-unit font size rendering in all browsers.
         */
         code,
         kbd,
         pre,
         samp {
         font-family: monospace, monospace;
         font-size: 1em;
         }
         /* Forms
         ========================================================================== */
         /**
         * Known limitation: by default, Chrome and Safari on OS X allow very limited
         * styling of `select`, unless a `border` property is set.
         */
         /**
         * 1. Correct color not being inherited.
         *    Known issue: affects color of disabled elements.
         * 2. Correct font properties not being inherited.
         * 3. Address margins set differently in Firefox 4+, Safari, and Chrome.
         */
         button,
         input,
         optgroup,
         select,
         textarea {
         color: inherit; /* 1 */
         font: inherit; /* 2 */
         margin: 0; /* 3 */
         }
         /**
         * Address `overflow` set to `hidden` in IE 8/9/10/11.
         */
         button {
         overflow: visible;
         }
         /**
         * Address inconsistent `text-transform` inheritance for `button` and `select`.
         * All other form control elements do not inherit `text-transform` values.
         * Correct `button` style inheritance in Firefox, IE 8/9/10/11, and Opera.
         * Correct `select` style inheritance in Firefox.
         */
         button,
         select {
         text-transform: none;
         }
         /**
         * 1. Avoid the WebKit bug in Android 4.0.* where (2) destroys native `audio`
         *    and `video` controls.
         * 2. Correct inability to style clickable `input` types in iOS.
         * 3. Improve usability and consistency of cursor style between image-type
         *    `input` and others.
         */
         button,
         html input[type="button"],
         input[type="reset"],
         input[type="submit"] {
         -webkit-appearance: button; /* 2 */
         cursor: pointer; /* 3 */
         }
         /**
         * Re-set default cursor for disabled elements.
         */
         button[disabled],
         html input[disabled] {
         cursor: default;
         }
         /**
         * Remove inner padding and border in Firefox 4+.
         */
         button::-moz-focus-inner,
         input::-moz-focus-inner {
         border: 0;
         padding: 0;
         }
         /**
         * Address Firefox 4+ setting `line-height` on `input` using `!important` in
         * the UA stylesheet.
         */
         input {
         line-height: normal;
         }
         /**
         * It's recommended that you don't attempt to style these elements.
         * Firefox's implementation doesn't respect box-sizing, padding, or width.
         *
         * 1. Address box sizing set to `content-box` in IE 8/9/10.
         * 2. Remove excess padding in IE 8/9/10.
         */
         input[type="checkbox"],
         input[type="radio"] {
         box-sizing: border-box; /* 1 */
         padding: 0; /* 2 */
         }
         /**
         * Fix the cursor style for Chrome's increment/decrement buttons. For certain
         * `font-size` values of the `input`, it causes the cursor style of the
         * decrement button to change from `default` to `text`.
         */
         input[type="number"]::-webkit-inner-spin-button,
         input[type="number"]::-webkit-outer-spin-button {
         height: auto;
         }
         /**
         * 1. Address `appearance` set to `searchfield` in Safari and Chrome.
         * 2. Address `box-sizing` set to `border-box` in Safari and Chrome.
         */
         input[type="search"] {
         -webkit-appearance: textfield; /* 1 */
         box-sizing: content-box; /* 2 */
         }
         /**
         * Remove inner padding and search cancel button in Safari and Chrome on OS X.
         * Safari (but not Chrome) clips the cancel button when the search input has
         * padding (and `textfield` appearance).
         */
         input[type="search"]::-webkit-search-cancel-button,
         input[type="search"]::-webkit-search-decoration {
         -webkit-appearance: none;
         }
         /**
         * Define consistent border, margin, and padding.
         */
         fieldset {
         border: 1px solid #c0c0c0;
         margin: 0 2px;
         padding: .35em .625em .75em;
         }
         /**
         * 1. Correct `color` not being inherited in IE 8/9/10/11.
         * 2. Remove padding so people aren't caught out if they zero out fieldsets.
         */
         legend {
         border: 0; /* 1 */
         padding: 0; /* 2 */
         }
         /**
         * Remove default vertical scrollbar in IE 8/9/10/11.
         */
         textarea {
         overflow: auto;
         }
         /**
         * Don't inherit the `font-weight` (applied by a rule above).
         * NOTE: the default cannot safely be changed in Chrome and Safari on OS X.
         */
         optgroup {
         font-weight: bold;
         }
         /* Tables
         ========================================================================== */
         /**
         * Remove most spacing between table cells.
         */
         table {
         border-collapse: collapse;
         border-spacing: 0;
         }
         td,
         th {
         padding: 0;
         }
         .clearfix:after {
         content: "";
         display: block;
         clear: both;
         }
         .ellipsis {
         white-space: nowrap; /* 1 */
         text-overflow: ellipsis; /* 2 */
         overflow: hidden;
         }
         html {
         box-sizing: border-box;
         }
         *,
         *:before,
         *:after {
         box-sizing: inherit;
         }
         * {
         max-height: 1000000px;
         }
         body {
         color: #000;
         background: #e8e6e7;
         font: 16px/1.2 "Poppins", "Arial", "Helvetica", sans-serif;
         min-width: 320px;
         -webkit-font-smoothing: antialiased;
         -moz-osx-font-smoothing: grayscale;
         }
         img {
         max-width: 100%;
         height: auto;
         vertical-align: top;
         }
         .gm-style img {
         max-width: none;
         }
         h1,
         .h1,
         h2,
         .h2,
         h3,
         .h3,
         h4,
         .h4,
         h5,
         .h5,
         h6,
         .h6,
         .h {
         font-family: inherit;
         font-weight: bold;
         margin: 0 0 .5em;
         color: inherit;
         }
         h1,
         .h1 {
         font-size: 30px;
         }
         h2,
         .h2 {
         font-size: 27px;
         }
         h3,
         .h3 {
         font-size: 24px;
         }
         h4,
         .h4 {
         font-size: 21px;
         }
         h5,
         .h5 {
         font-size: 17px;
         }
         h6,
         .h6 {
         font-size: 15px;
         }
         p {
         margin: 0 0 1em;
         }
         a {
         -webkit-transition: all .4s ease;
         transition: all .4s ease;
         text-decoration: none;
         color: #39f;
         }
         a:hover,
         a:focus {
         text-decoration: none;
         }
         form,
         fieldset {
         margin: 0;
         padding: 0;
         border-style: none;
         }
         input[type="text"],
         input[type="tel"],
         input[type="email"],
         input[type="search"],
         input[type="password"],
         textarea {
         -webkit-appearance: none;
         -webkit-border-radius: 0;
         box-sizing: border-box;
         border: 1px solid #999;
         padding: .4em .7em;
         }
         input[type="text"]:focus,
         input[type="tel"]:focus,
         input[type="email"]:focus,
         input[type="search"]:focus,
         input[type="password"]:focus,
         textarea:focus {
         border-color: #000;
         }
         select {
         -webkit-border-radius: 0;
         }
         textarea {
         resize: vertical;
         vertical-align: top;
         }
         button,
         input[type="button"],
         input[type="reset"],
         input[type="file"],
         input[type="submit"] {
         -webkit-appearance: none;
         -webkit-border-radius: 0;
         cursor: pointer;
         }
         [class^="icon-"],
         [class*=" icon-"] {
         /* use !important to prevent issues with browser extensions that change fonts */
         font-family: "icomoon" !important;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         text-transform: none;
         line-height: 1; /* Better Font Rendering =========== */
         -webkit-font-smoothing: antialiased;
         -moz-osx-font-smoothing: grayscale;
         }
         .icon-instagram:before {
         content: "\e900";
         }
         .icon-bookmark:before {
         content: "\e901";
         }
         .icon-search:before {
         content: "\e902";
         }
         .icon-facebook:before {
         content: "\e903";
         }
         .icon-twitter:before {
         content: "\e904";
         }
         .icon-pinterest:before {
         content: "\e905";
         }
         .icon-feed:before {
         content: "\e906";
         }
         .icon-alarm:before {
         content: "\e907";
         }
         .icon-heart:before {
         content: "\e908";
         }
         #wrapper {
         position: relative;
         overflow: hidden;
         width: 100%;
         }
         .container {
         position: relative;
         max-width: 630px;
         padding: 0 15px;
         margin: 0 auto;
         }
         .header {
         position: relative;
         background: #747474;
         padding: 28px 0 15px;
         }
         .header h1 {
         font-size: 25px;
         line-height: 28px;
         font-weight: 400;
         text-align: center;
         color: #fff;
         margin: 0;
         }
         .header-holder {
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         -webkit-box-pack: justify;
         -ms-flex-pack: justify;
         justify-content: space-between;
         margin: 0 0 40px;
         }
         .header-holder .option-list {
         margin: 0;
         padding: 0;
         list-style: none;
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         margin: 0 -5px;
         }
         .header-holder .option-list li {
         position: relative;
         padding: 0 5px;
         }
         .header-holder .option-list a {
         color: #fff;
         }
         .header-holder .option-list a i {
         font-size: 20px;
         line-height: 1;
         display: block;
         }
         .header-holder .option-list a:hover {
         opacity: .8;
         }
         .logo {
         display: inline-block;
         vertical-align: top;
         font-size: 30px;
         line-height: 34px;
         font-weight: 400;
         position: relative;
         padding: 0 0 13px;
         }
         .logo:before {
         width: 90px;
         height: 2px;
         content: "";
         position: absolute;
         left: 50%;
         bottom: 0;
         background: #fff;
         margin: 0 0 0 -45px;
         }
         .logo a {
         display: block;
         color: #fff;
         }
         .logo a:hover {
         opacity: .8;
         }
         .main {
         position: relative;
         }
         .content-area {
         position: relative;
         padding: 30px 0 0;
         }
         .content-block {
         position: relative;
         border-radius: 15px;
         background: #fff;
         padding: 25px 0;
         margin-bottom: 30px;
         }
         .content-block h2 {
         font-size: 20px;
         line-height: 24px;
         font-weight: 600;
         color: #000;
         position: relative;
         text-align: center;
         padding: 0 0 12px;
         margin: 0 0 15px;
         }
         .content-block h2:before {
         width: 84px;
         height: 2px;
         content: "";
         position: absolute;
         left: 50%;
         bottom: 0;
         background: #000;
         margin: 0 0 0 -42px;
         }
         .content-block .heading-area {
         position: relative;
         background: #535353;
         padding: 15px 22px;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         color: #fff;
         -webkit-box-pack: justify;
         -ms-flex-pack: justify;
         justify-content: space-between;
         }
         .content-block .heading-area .heading {
         font-size: 22px;
         line-height: 1.1;
         display: block;
         font-weight: 700;
         }
         .content-block .heading-area .heading .text {
         font-weight: 400;
         font-size: 16px;
         display: block;
         margin: 0 0 5px;
         }
         .content-block .heading-area .link {
         display: block;
         }
         .content-block .heading-area a {
         color: #fff;
         }
         .content-block .heading-area a:hover {
         opacity: .8;
         }
         .content-block .block-row {
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         padding: 22px 25px 0;
         margin: 0 -22px;
         }
         .content-block .block-col {
         position: relative;
         width: 33.333%;
         padding: 0 22px;
         margin: 0 0 18px;
         }
         .content-block .block {
         position: relative;
         }
         .content-block .image-holder {
         box-shadow: 0 4px 10px rgba(0, 0, 0, .08);
         width: 148px;
         position: relative;
         margin: 0 0 10px;
         }
         .content-block .image-holder img {
         display: block;
         margin: 0 auto;
         }
         .content-block .image-holder img {
         display: block;
         width: 100%;
         height: auto;
         }
         .content-block .description {
         position: relative;
         text-align: center;
         }
         .content-block .description .title {
         font-size: 14px;
         line-height: 17px;
         font-weight: 600;
         display: block;
         color: #2a2a2a;
         margin: 0 0 7px;
         }
         .content-block .description .price-holder {
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         -webkit-box-pack: justify;
         -ms-flex-pack: justify;
         justify-content: space-between;
         padding: 0 15px;
         margin: 0 0 8px;
         }
         .content-block .description .price {
         font-size: 13px;
         line-height: 16px;
         font-weight: 500;
         display: block;
         color: #535353;
         }
         .content-block .description .old-price {
         font-size: 13px;
         line-height: 16px;
         font-weight: 500;
         text-decoration: line-through;
         display: block;
         color: #a7a7a7;
         }
         .content-block .description .discount {
         font-size: 13px;
         line-height: 16px;
         font-style: italic;
         font-weight: 300;
         display: block;
         color: #000;
         }
         .content-block .btn-more {
         font-size: 20px;
         line-height: 24px;
         position: relative;
         margin: 0 auto;
         display: block;
         width: 126px;
         height: 38px;
         color: #fff;
         text-align: center;
         background: #747474;
         padding: 6px 3px;
         }
         .content-block .btn-more:hover {
         background: #414141;
         }
         .footer {
         position: relative;
         background: #747474;
         padding: 40px 0 0;
         color: #fff;
         }
         .footer-holder {
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: start;
         -ms-flex-align: start;
         align-items: flex-start;
         padding: 0 0 20px;
         }
         .footer-holder .store-btn {
         margin: 0;
         padding: 0;
         list-style: none;
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: start;
         -ms-flex-align: start;
         align-items: flex-start;
         margin: 0 -3px;
         }
         .footer-holder .store-btn li {
         position: relative;
         padding: 0 3px;
         }
         .footer-holder .store-btn a {
         display: block;
         }
         .footer-holder .store-btn a img {
         display: block;
         }
         .footer-holder .store-btn a:hover {
         opacity: .8;
         }
         .address-area {
         position: relative;
         }
         .address-area .wrap {
         font-size: 11px;
         line-height: 18px;
         position: relative;
         text-align: right;
         }
         .address-area .wrap .text {
         display: block;
         }
         .address-area .wrap a {
         color: #fff;
         }
         .address-area .wrap a:hover {
         opacity: .8;
         }
         .social-networks {
         margin: 0;
         padding: 0;
         list-style: none;
         position: relative;
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -ms-flex-wrap: wrap;
         flex-wrap: wrap;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         -webkit-box-pack: end;
         -ms-flex-pack: end;
         justify-content: flex-end;
         margin: 0 -6px 24px;
         }
         .social-networks li {
         position: relative;
         padding: 0 6px;
         }
         .social-networks a {
         width: 38px;
         height: 38px;
         background: #fff;
         color: #000;
         display: block;
         border-radius: 50%;
         text-align: center;
         }
         .social-networks a i {
         font-size: 18px;
         line-height: 38px;
         display: block;
         }
         .social-networks a:hover {
         background: #ccc;
         }
         .footer-note {
         font-size: 11px;
         line-height: 18px;
         position: relative;
         text-align: center;
         padding: 22px 0 18px;
         z-index: 2;
         }
         .footer-note:before {
         content: "";
         position: absolute;
         left: -9999px;
         right: -9999px;
         top: 0;
         bottom: 0;
         background: #626262;
         z-index: -1;
         }
         .footer-note p {
         position: relative;
         margin: 0;
         }
         .footer-note a {
         color: #fff;
         }
         .footer-note a:hover {
         opacity: .8;
         }
         @media (min-width: 1024px) {
         .footer-note p {
         margin: 0 -18px;
         }
         }
         @media (max-width: 599px) {
         .content-block .block-row {
         padding: 20px 15px;
         margin: 0 -15px;
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
         }
         .content-block .block-col {
         padding: 0 15px;
         width: 50%;
         }
         .content-block .image-holder {
         margin: 0 auto 10px;
         }
         .footer-holder {
         display: block;
         }
         .footer-holder .store-btn {
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
         margin: 0 0 15px;
         }
         .address-area .wrap {
         text-align: center;
         }
         .social-networks {
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
         margin: 0 0 20px;
         }
         }
         @media (max-width: 479px) {
         .header {
         padding: 15px 0;
         }
         .header h1 {
         font-size: 20px;
         line-height: 24px;
         }
         .header-holder {
         display: block;
         text-align: center;
         margin: 0 0 20px;
         }
         .header-holder .option-list {
         -webkit-box-pack: center;
         -ms-flex-pack: center;
         justify-content: center;
         }
         .logo {
         font-size: 24px;
         line-height: 27px;
         margin: 0 0 20px;
         }
         .logo:before {
         width: 70px;
         margin: 0 0 0 -35px;
         }
         .content-area {
         padding: 30px 0 0;
         }
         .content-block {
         padding: 20px 0;
         border-radius: 10px;
         }
         .content-block .heading-area {
         padding: 12px 15px;
         display: block;
         text-align: center;
         }
         .content-block .heading-area .heading {
         margin: 0 0 12px;
         }
         .content-block .block-col {
         width: 100%;
         }
         }
      </style>
   </head>
   <body>
<div id="wrapper"><header class="header">
<div class="container">
<div class="header-holder"><strong class="logo"><a href="#">Lussolicious</a></strong>
<ul class="option-list">
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
</ul>
</div>
<h1>Recommended for you</h1>
</div>
</header><main class="main">
<div class="content-area">
<div class="container">
<div class="content-block">
<h2>Huge discounts from</h2>
<div class="heading-area"><strong class="heading"><span class="text">On sale from</span>Boutique Moschino</strong> <span class="link"><a href="#">Shop now</a></span></div>
<div class="block-row">@foreach($products as $product)
<div class="block-col">
<div class="block">
<div class="image-holder"><img src="{{ $product->images[0] }}" alt="image-description" /></div>
<div class="description"><strong class="title">{{ $product['name'] }}</strong>
<div class="price-holder"><strong class="price">{{ $product['price'] }}</strong> <strong class="old-price">{{ $product['price'] }}</strong></div>
</div>
</div>
</div>
@endforeach</div>
<a class="btn-more" href="#">See more</a></div>
</div>
</div>
</main><footer class="footer">
<div class="container">
<div class="footer-holder">
<ul class="store-btn">
<li><a href="#"><img src="https://erp.amourint.com/newsletter/general/images/google-play.png" alt="google-play" /></a></li>
<li><a href="#"><img src="https://erp.amourint.com/newsletter/general/images/app-store.png" alt="app-store" /></a></li>
</ul>
<div class="address-area">
<ul class="social-networks">
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
<li>&nbsp;</li>
</ul>
<div class="wrap"><span class="text">&copy; 2020 Shopia Fashion Store Shopify. </span> <span class="text">All Rights Reserved. Ecommerce Software by Shopify.</span> <span class="text">Designed by Lussolicious.com</span></div>
</div>
</div>
<div class="footer-note">
<p>To make sure you don't miss out on the latest alerts and recommendations, add <a href="#">hello@Lussolicious.com</a> to your address book. Click here to change your settings or unsubscribe from our emails.</p>
</div>
</div>
</footer></div>
</body>
</html>