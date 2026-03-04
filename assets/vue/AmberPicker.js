import Bootstrap from "/slothsoft@farah/js/Bootstrap";
function bootstrap() {
  window.document.querySelectorAll("amber-embed[mode~='picker']").forEach((node) => {
    node.addEventListener("contextmenu", (eve) => alert(eve));
  });
}
Bootstrap.run(bootstrap);
