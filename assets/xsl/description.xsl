<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml"
	xmlns:html="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:sfm="http://schema.slothsoft.net/farah/module" xmlns:sfs="http://schema.slothsoft.net/farah/sitemap">

	<xsl:template match="/*">
		<div data-dict="">
			<xsl:text>description:</xsl:text>
			<xsl:value-of select="*[@name = 'sites']//sfs:page[@current]/@name" />
		</div>
	</xsl:template>
</xsl:stylesheet>
