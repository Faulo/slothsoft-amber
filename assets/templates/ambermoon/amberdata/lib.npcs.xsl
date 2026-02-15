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
		<xsl:variable name="characters" select="sse:archive[@name='NPC_char.amb']/*" />
		<xsl:variable name="dialog" select="sse:archive[@name='NPC_texts.amb']/*" />
		<xsl:if test="count($characters)">
			<saa:npc-list>
				<xsl:variable name="categories" select="saa:getDictionary('classes')" />
				<xsl:for-each select="$categories">
					<xsl:variable name="category" select="." />
					<saa:npc-category name="{@val}">
						<xsl:for-each select="$characters">
							<xsl:if test=".//*[@name='class']/@value = $category/@key">
								<xsl:call-template name="extract-npc">
									<xsl:with-param name="id" select="position()" />
									<xsl:with-param name="dialog" select="$dialog[@file-name = current()/@file-name]" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</saa:npc-category>
				</xsl:for-each>
			</saa:npc-list>
		</xsl:if>
	</xsl:template>

	<xsl:template name="extract-npc">
		<xsl:param name="root" select="." />
		<xsl:param name="id" />
		<xsl:param name="dialog" select="/.." />
		<saa:npc>
			<xsl:call-template name="extract-character">
				<xsl:with-param name="root" select="$root" />
				<xsl:with-param name="id" select="$id" />
				<xsl:with-param name="dialog" select="$dialog" />
			</xsl:call-template>
			<xsl:call-template name="extract-race-instance">
				<xsl:with-param name="root" select="$root" />
			</xsl:call-template>
			<xsl:call-template name="extract-class-instance">
				<xsl:with-param name="root" select="$root" />
			</xsl:call-template>
		</saa:npc>
	</xsl:template>
</xsl:stylesheet>