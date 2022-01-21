<xsl:stylesheet version="1.0" 
	xmlns="http://schema.slothsoft.net/amber/amberdata"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="globals/dictionary" />
	<xsl:import href="globals/extract" />
	
	<xsl:template match="/*">
		<amberdata version="0.1">
			<xsl:apply-templates select="*/sse:savegame.editor" />
		</amberdata>
	</xsl:template>

	<xsl:template match="sse:savegame.editor">
		<xsl:variable name="characters" select="sse:archive[@name='Party_char.amb']/*" />
		<xsl:variable name="dialog" select="sse:archive[@name='Party_texts.amb']/*" />
		<xsl:if test="count($characters)">
			<saa:pc-list>
				<xsl:variable name="categories" select="saa:getDictionary('classes')" />
				<xsl:for-each select="$categories">
					<xsl:variable name="category" select="." />
					<saa:pc-category name="{@val}">
						<xsl:for-each select="$characters">
							<xsl:if test=".//*[@name='class']/@value = $category/@key">
								<xsl:call-template name="extract-pc">
									<xsl:with-param name="id" select="position()" />
									<xsl:with-param name="dialog" select="$dialog[@file-name = current()/@file-name]" />
								</xsl:call-template>
							</xsl:if>
						</xsl:for-each>
					</saa:pc-category>
				</xsl:for-each>
			</saa:pc-list>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="extract-pc">
		<xsl:param name="root" select="." />
		<xsl:param name="id" />
		<xsl:param name="dialog" select="/.." />
		<saa:pc>
			<xsl:call-template name="extract-character">
				<xsl:with-param name="root" select="$root" />
				<xsl:with-param name="id" select="$id" />
				<xsl:with-param name="dialog" select="$dialog" />
			</xsl:call-template>
			<xsl:call-template name="extract-race">
				<xsl:with-param name="root" select="$root" />
			</xsl:call-template>
			<xsl:call-template name="extract-class-instance">
				<xsl:with-param name="root" select="$root" />
			</xsl:call-template>
		</saa:pc>
	</xsl:template>
</xsl:stylesheet>