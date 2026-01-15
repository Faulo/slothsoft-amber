<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/dictionary" />
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/savegame" />
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/editor" />
	<xsl:import href="farah://slothsoft@amber/templates/ambermoon/editor/globals/picker" />

	<xsl:template match="sse:archive" mode="form-content">
		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktiver Eintrag:'" />
			<xsl:with-param name="options" select="sse:file/@file-name" />
			<xsl:with-param name="list">
				<xsl:for-each select="sse:file">
					<li>
						<xsl:call-template name="savegame.flex">
							<xsl:with-param name="items">
								<xsl:for-each select=".//sse:instruction[@type = 'string-dictionary'] | .//sse:string[not(parent::sse:instruction[@type = 'string-dictionary'])]">
									<div>
										<xsl:apply-templates select="." mode="item" />
									</div>
								</xsl:for-each>
							</xsl:with-param>
						</xsl:call-template>
					</li>
				</xsl:for-each>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>
</xsl:stylesheet>