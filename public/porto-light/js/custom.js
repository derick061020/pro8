/* Add here all your JS customizations */
$(function() {
  $(".switcher-hover").mouseenter(function() {
    $("#switcher-list").toggleClass("fade show active");
    $("#switcher-top").toggleClass("fade show active");
  });
  $(".switcher-hover").mouseleave(function() {
    $("#switcher-list").toggleClass("fade show active");
    $("#switcher-top").toggleClass("fade show active");
  });
});

document.addEventListener('click', function(event) {
    var trigger = event.target.closest('.style-switcher-open');

    if (!trigger) {
        return;
    }

    var styleSwitcher = document.getElementById('styleSwitcher') || document.querySelector('.style-switcher');

    if (!styleSwitcher) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();

    var panelWidth = styleSwitcher.offsetWidth || 325;
    var shouldClose = trigger.classList.contains('close-config') || styleSwitcher.classList.contains('active');

    styleSwitcher.classList.toggle('active', !shouldClose);
    styleSwitcher.style.right = shouldClose ? '-' + panelWidth + 'px' : '0px';
}, true);
