//import("@slothsoft/farah")
//	.then(farah => {
//		farah.Module.resolveToDocument("farah://slothsoft@amber")
//			.then(document => farah.DOM.DOMHelper.evaluate("//sfm:asset-manifest", document))
//			.then(firstNode => farah.DOM.DOMHelper.stringify(firstNode[0]))
//			.then(xml => {alert(xml);});
//	});

function viewMap(mapNode, tilesetNode) {
	Promise.all([import("./index"), import("pixi.js"), import("@slothsoft/farah")])
		.then(([amber, pixi, farah]) => {
			new amber.MapViewer.App(mapNode, tilesetNode, pixi);
		});
}