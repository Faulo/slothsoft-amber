export default function() {
  return 'Hello';
}

function getComponent () {
  var element = document.createElement('pre');
  element.innerHTML = 'World';
  return element;
}

document.body.appendChild(getComponent());