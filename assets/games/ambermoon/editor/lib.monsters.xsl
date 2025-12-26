<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns="http://www.w3.org/1999/xhtml" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor">

	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/dictionary" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/savegame" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/editor" />
	<xsl:import href="farah://slothsoft@amber/games/ambermoon/editor/globals/picker" />

	<xsl:template match="sse:archive[@name='Monster_char_data.amb']" mode="form-content">
		<xsl:call-template name="picker.items" />

		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktives Monster:'" />
			<xsl:with-param name="optionTokens" select="saa:optionsForFiles(sse:file, 'monster-ids')" />
			<xsl:with-param name="list">
				<xsl:for-each select="sse:file">
					<li>
						<xsl:call-template name="savegame.flex">
							<xsl:with-param name="items">
								<div>
									<xsl:call-template name="savegame.amber.testing" />
								</div>
								<xsl:call-template name="savegame.amber.events" />
								<div>
									<xsl:call-template name="savegame.amber.character-common" />
									<xsl:call-template name="savegame.amber.character-combat" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-gfx" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-race" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-class" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-equipment" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-inventory" />
								</div>
								<div>
									<xsl:call-template name="savegame.amber.character-spells" />
								</div>
							</xsl:with-param>
						</xsl:call-template>
					</li>
				</xsl:for-each>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>

	<xsl:template match="sse:archive[@name='Monster_groups.amb']" mode="form-content">
		<xsl:call-template name="picker.items" />

		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktive Monstergruppe:'" />
			<xsl:with-param name="optionTokens" select="saa:optionsForFiles(sse:file, 'monstergroup-ids')" />
			<xsl:with-param name="list">
				<xsl:for-each select="sse:file">
					<li>
						<table>
							<tbody>
								<xsl:for-each select="sse:instruction/sse:group">
									<tr>
										<xsl:for-each select="sse:instruction/sse:group">
											<td>
												<xsl:apply-templates select="*" mode="form-content" />
											</td>
										</xsl:for-each>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</li>
				</xsl:for-each>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>
</xsl:stylesheet>