
document.querySelectorAll('.section-tab').forEach(function (tab) {
  tab.addEventListener('click', function () {

    var strip = tab.parentElement;

    strip.querySelectorAll(':scope > .section-tab').forEach(function (t) {
      t.classList.remove('active');
    });
    tab.classList.add('active');

    var panels = [];
    var node = strip.nextElementSibling;
    while (node) {
      if (node.classList && node.classList.contains('section-tabs')) break;
      if (node.classList && node.classList.contains('section-panel')) {
        panels.push(node);
      }
      node = node.nextElementSibling;
    }

    panels.forEach(function (p) { p.classList.remove('active'); });

    var target = document.getElementById(tab.dataset.target);
    if (target) {
      target.classList.add('active');
    }
  });
});
