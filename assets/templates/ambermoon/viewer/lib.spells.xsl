<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata">
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/viewer/globals/amber-list" />


	<xsl:template match="saa:spellbook-list">
		<xsl:call-template name="amber-list" />
	</xsl:template>

	<xsl:template match="saa:spellbook">
		<xsl:call-template name="amber-category" />
	</xsl:template>

	<xsl:template match="saa:spell">
		<article class="amber-spell">
			<div class="amber-spell__id amber-text amber-text--yellow">
				<xsl:value-of select="@name" />
			</div>
			<div class="amber-spell__name amber-text amber-text--silver">
				<xsl:copy-of select="." />
			</div>
		</article>
	</xsl:template>
</xsl:stylesheet>
