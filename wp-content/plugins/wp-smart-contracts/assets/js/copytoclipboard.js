function copyToClipboard(str, divid) {
  const el = document.createElement('textarea');
  el.value = str;
  el.setAttribute('readonly', '');
  el.style.position = 'absolute';
  el.style.left = '-9999px';
  document.body.appendChild(el);
  el.select();
  document.execCommand('copy');
  document.body.removeChild(el);
  jQuery("#" + divid).after('<span id="wpsc-copy-msg-' + divid + '">' + copied + '</span>');
  jQuery("#wpsc-copy-msg-" + divid).fadeOut(500);
}
