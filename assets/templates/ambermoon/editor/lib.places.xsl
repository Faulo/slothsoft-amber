<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/dictionary" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/savegame" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/editor" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/picker" />

	<xsl:template match="sse:archive[@name='Place_data']" mode="form-content">
		<xsl:for-each select="sse:file">
			<xsl:call-template name="savegame.flex">
				<xsl:with-param name="items">
					<div>
						<xsl:apply-templates select="*[@name = 'names']" mode="item" />
					</div>
					<div>
						<xsl:call-template name="savegame.tabs">
							<xsl:with-param name="label" select="'Aktiver Ort:'" />
							<xsl:with-param name="options" select="*[@name = 'names']/*/@value" />
							<xsl:with-param name="list">
								<xsl:for-each select="*[@name = 'places']/*">
									<li>
										<xsl:call-template name="savegame.table">
											<xsl:with-param name="items">
												<xsl:apply-templates select="*" mode="item" />
											</xsl:with-param>
										</xsl:call-template>
									</li>
								</xsl:for-each>
							</xsl:with-param>
						</xsl:call-template>
					</div>
				</xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>