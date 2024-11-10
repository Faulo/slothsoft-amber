<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:sfs="http://schema.slothsoft.net/farah/sitemap">

	<xsl:template match="/*">
		<div>
		<!-- 
			<script type="module"><![CDATA[
import * as Module from "/slothsoft@farah/js/Module";

Module.resolveToDocument("./Downloads")
	.then(document => alert(document.documentElement.namespaceURI));

			]]></script>
			<script><![CDATA[
/*
import("/slothsoft@farah/js/Farah/Module")
	.then(module => module.resolveToDocument("/slothsoft@amber"))
	.then(document => alert(document));
//*/
			]]></script>
			 -->
			<span data-dict=".">
				<xsl:text>description:</xsl:text>
				<xsl:value-of select="*[@name = 'sites']//sfs:page[@current]/@name" />
			</span>
<!-- 			<div ng-app="slothsoft@amber/Editor.App" /> -->
		</div>
	</xsl:template>
</xsl:stylesheet>
