<xsl:stylesheet version="1.0" xmlns="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/dictionary" />
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/amberdata/globals/extract" />

	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame" />
		</amberdata>
	</xsl:template>

	<xsl:template match="sse:savegame">
		<xsl:variable name="spell-types" select="sse:archive[@name='AM2_BLIT']//*[@name='spell-types']/*" />
		<xsl:variable name="spell-data" select="sse:archive[@name='AM2_BLIT']//*[@name='spell-data']/*" />
		<xsl:variable name="spell-names" select="sse:archive[@name='AM2_BLIT']//*[@name='spell-names']/*" />
		<xsl:if test="count($spell-types)">
			<spellbook-list>
				<xsl:for-each select="$spell-types">
					<xsl:variable name="pos" select="position()" />
					<xsl:call-template name="extract-spellbook">
						<xsl:with-param name="spellbook-id" select="$pos - 1" />
						<xsl:with-param name="spellbook-type" select="$spell-types[$pos]/@value" />
						<xsl:with-param name="spellbook-data" select="$spell-data[$pos]/*" />
						<xsl:with-param name="spellbook-names" select="$spell-names[$pos]/*" />
					</xsl:call-template>
				</xsl:for-each>
			</spellbook-list>
		</xsl:if>
	</xsl:template>

	<xsl:template name="extract-spellbook">
		<xsl:param name="spellbook-id" />
		<xsl:param name="spellbook-type" />
		<xsl:param name="spellbook-data" />
		<xsl:param name="spellbook-names" />
		<spellbook id="{$spellbook-id}" name="{$spellbook-type}">
			<xsl:for-each select="$spellbook-names">
				<xsl:variable name="pos" select="position()" />
				<xsl:call-template name="extract-spell">
					<xsl:with-param name="spell-id" select="$pos - 1" />
					<xsl:with-param name="spell-data" select="$spellbook-data[$pos]" />
					<xsl:with-param name="spell-name" select="$spellbook-names[$pos]/@value" />
				</xsl:call-template>
			</xsl:for-each>
		</spellbook>
	</xsl:template>

	<xsl:template name="extract-spell">
		<xsl:param name="spell-id" />
		<xsl:param name="spell-name" />
		<xsl:param name="spell-data" />

		<xsl:if test="string-length($spell-name)">
			<spell id="{$spell-id}" name="{$spell-name}">
				<xsl:apply-templates select="$spell-data" mode="attr" />
			</spell>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>