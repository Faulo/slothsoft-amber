
function launchEditor() {
	Promise.all([
		import("./index"),
		import("@slothsoft/farah"),
		import("react"),
	])
	.then(([amber, farah, react]) => {
		console.log(react);
	});
}