<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata"
	xmlns:sse="http://schema.slothsoft.net/savegame/editor"
	xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
	xmlns:exsl="http://exslt.org/common">
	
	<xsl:template name="maps.data-file">
		<xsl:call-template name="picker.tileset-icons"/>

		<xsl:variable name="fileList" select="sse:file" />
		<xsl:variable name="nameList"
			select="key('dictionary-option', 'map-ids')[number(@key) = $fileList/@file-name]" />

		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktive Karte:'" />
			<xsl:with-param name="options" select="$nameList/@val" />
			<xsl:with-param name="list">
				<xsl:for-each select="$nameList">
					<xsl:for-each select="$fileList[@file-name = number(current()/@key)]">
						<li>
							<div>
								<xsl:call-template name="savegame.table">
									<xsl:with-param name="label" select="'data'" />
									<xsl:with-param name="items">
										<xsl:apply-templates select=".//*[@name='data']/* | .//*[@name='unknown']" mode="item" />
									</xsl:with-param>
								</xsl:call-template>
							</div>
							<xsl:call-template name="savegame.amber.mobs" />
							<xsl:call-template name="savegame.amber.fields" />
							<xsl:call-template name="savegame.amber.events" />
						</li>
					</xsl:for-each>
				</xsl:for-each>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="maps.tileset-file">
		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktives Tileset:'" />
			<xsl:with-param name="options" select="sse:file/@file-name" />
			<xsl:with-param name="list">
				<xsl:for-each select="sse:file">
					<xsl:variable name="tilesetId" select="number(@file-name)" />
					<li>
						<xsl:call-template name="savegame.flex">
							<xsl:with-param name="items">
								<xsl:for-each select=".//*[@name='data']">
									<div>
										<xsl:call-template name="savegame.table">
											<xsl:with-param name="label" select="'data'" />
											<xsl:with-param name="items">
												<xsl:apply-templates select="*" mode="item" />
											</xsl:with-param>
										</xsl:call-template>
									</div>
								</xsl:for-each>
								<div>
									<xsl:for-each select=".//*[@name = 'tiles']">
										<h3 class="name">tiles</h3>
										<table data-tileset-icon="{$tilesetId}" data-palette="1">
											<thead>
												<tr>
													<td>tile-id</td>
													<td></td>
													<td>image-id</td>
													<td>count</td>

													<td>foot</td>
													<td>horse</td>
													<td>raft</td>
													<td>ship</td>
													<td>broom</td>
													<td>eagle</td>
													<td>cape</td>
													<td>water</td>

													<td>data</td>
													<td>color</td>
												</tr>
											</thead>
											<tbody>
												<xsl:for-each select="*/*">
													<xsl:if test="true() or *[@name='image-count']/@value &gt; 0">
														<tr>
															<td class="right-aligned">
																<xsl:value-of select="position()" />
															</td>
															<td>
																<amber-picker type="tileset-icon-{$tilesetId}" class="tile-picker" role="button"
																	tabindex="0">
																	<amber-tile-id value="{position()}" />
																</amber-picker>
															</td>
															<td>
																<xsl:apply-templates select="sse:integer[1]" mode="form-content" />
															</td>
															<td>
																<xsl:apply-templates select="sse:integer[2]" mode="form-content" />
															</td>
															<xsl:for-each select="*[@name='mobility']/*">
																<td>
																	<xsl:apply-templates select="." mode="form-content" />
																</td>
															</xsl:for-each>
															<td>
																<xsl:apply-templates select="*[@name='testing']/*" mode="form-content" />
															</td>
															<td>
																<xsl:apply-templates select="sse:select" mode="form-content" />
															</td>
														</tr>
													</xsl:if>
												</xsl:for-each>
											</tbody>
										</table>
									</xsl:for-each>


									<xsl:for-each select=".//*[@name = 'tiles-lab']">
										<h3 class="name">tiles</h3>
										<table data-tileset-lab="{$tilesetId}" data-palette="1">
											<thead>
												<tr>
													<td>tile-id</td>
													<td>data</td>
												</tr>
											</thead>
											<tbody>
												<xsl:for-each select="*/*">
													<xsl:if test="true() or *[@name='image-count']/@value &gt; 0">
														<tr>
															<td class="right-aligned">
																<xsl:value-of select="position()" />
															</td>
															<td>
																<xsl:apply-templates select="." mode="form-content" />
															</td>
														</tr>
													</xsl:if>
												</xsl:for-each>
											</tbody>
										</table>
									</xsl:for-each>
								</div>
							</xsl:with-param>
						</xsl:call-template>
					</li>
				</xsl:for-each>
			</xsl:with-param>
		</xsl:call-template>
	</xsl:template>
	
	<xsl:template name="maps.text-file">
		<xsl:call-template name="savegame.tabs">
			<xsl:with-param name="label" select="'Aktive Karte:'" />
			<xsl:with-param name="optionTokens" select="saa:optionsForFiles(sse:file, 'map-ids')" />
			<xsl:with-param name="list">
				<xsl:for-each select="sse:file">
					<li>
						<xsl:call-template name="savegame.flex">
							<xsl:with-param name="items">
								<xsl:for-each select="*">
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