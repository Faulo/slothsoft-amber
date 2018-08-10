
import dep1 from './Dep';

function getComponent () {
  var element = document.createElement('pre');
  element.innerHTML = dep1();
  return element;
}

document.body.appendChild(getComponent());