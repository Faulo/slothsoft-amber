<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns="http://www.w3.org/1999/xhtml" xmlns:html="http://www.w3.org/1999/xhtml"
	xmlns:sfm="http://schema.slothsoft.net/farah/module"
	xmlns:saa="http://schema.slothsoft.net/amber/amberdata" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:str="http://exslt.org/strings" xmlns:sfx="http://schema.slothsoft.net/farah/xslt"
	extension-element-prefixes="str">
	
	<xsl:import href="farah://slothsoft@farah/xsl/xslt" />

	<xsl:variable name="amberdata" select="/*/*[@name='amberdata']/saa:amberdata" />
	
	<xsl:key name="dictionary-option" match="/*/*[@name='dictionaries']/saa:amberdata/saa:dictionary-list/saa:dictionary/saa:option"
		use="../@dictionary-id" />
		
	<xsl:template match="/*">
		<div class="Amber Editor">
			<script type="application/javascript"><![CDATA[
var savegameEditor;
window.addEventListener(
	"DOMContentLoaded",
	function(eve) {
		let node;
		
		node = document.querySelector(".Amber.Editor");
		if (node) {
			savegameEditor = new SavegameEditor(node);
		}
	},
	false
);
			]]></script>
			<xsl:apply-templates select="*[@name = 'list']" mode="itemlist" />
			<div class="popup" onclick="savegameEditor.closePopup(arguments[0])" />
		</div>
	</xsl:template>

	<xsl:template match="saa:*">
		<xsl:apply-templates select="." mode="itemlist" />
	</xsl:template>

	<xsl:template match="saa:portrait-category | saa:item-category | saa:monster-category | saa:npc-category | saa:pc-category" mode="itemlist">
		<xsl:if test="*">
			<details data-template="flex">
				<summary>
					<h2>
						Kategorie:
						<span class="green">
							<xsl:value-of select="@name" />
						</span>
					</h2>
				</summary>
				<ul>
					<xsl:for-each select="*">
						<xsl:sort select="saa:class-instance/@experience" data-type="number"/>
						<xsl:sort select="@level" data-type="number"/>
						<li>
							<xsl:apply-templates select="." mode="itemlist" />
						</li>
					</xsl:for-each>
				</ul>
			</details>
		</xsl:if>
	</xsl:template>

	<xsl:template match="saa:class-list" mode="itemlist">
		<details data-template="flex">
			<summary>
				<h2>
					Fähigkeiten
				</h2>
			</summary>
			<ul>
				<xsl:for-each select="*">
					<li>
						<xsl:apply-templates select="." mode="itemlist" />
					</li>
				</xsl:for-each>
			</ul>
		</details>
		<details data-template="flex">
			<summary>
				<h2>
					Erfahrungstabelle
				</h2>
			</summary>
			<ul>
				<xsl:for-each select="*">
					<li>
						<xsl:apply-templates select="." mode="experience" />
					</li>
				</xsl:for-each>
			</ul>
		</details>
	</xsl:template>
	
	<xsl:template match="saa:spellbook-list" mode="itemlist">
		spellbook-list
	</xsl:template>

	<xsl:template match="saa:map-list" mode="itemlist">
		<xsl:choose>
			<xsl:when test="count(*) = 1">
				<xsl:for-each select="*">
					<button type="button" onclick="savegameEditor.viewMap(this.parentNode, '{@id}')">
						Weltkarte generieren:
						<span class="green">
							<xsl:value-of select="@name" />
						</span>
					</button>
					<template>
						<xsl:copy-of select="." />
					</template>
				</xsl:for-each>
			</xsl:when>
			<xsl:otherwise>
				<xsl:for-each select="*">
					<details ontoggle="savegameEditor.viewMap(this, '{@id}')">
						<summary>
							<h2>
								Karte:
								<span class="green">
									<xsl:value-of select="@name" />
								</span>
							</h2>
						</summary>
						<template>
							<xsl:copy-of select="." />
						</template>
					</details>
				</xsl:for-each>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="saa:class" mode="itemlist">
		<table class="ClassData">
			<caption><xsl:value-of select="@name"/></caption>
			<tbody>
				<xsl:for-each select="saa:skill">
					<tr class="right-aligned">
						<td>
							<xsl:value-of select="@name" />
							<xsl:text>:</xsl:text>
						</td>
						<td class="number">
							<xsl:value-of select="concat(@maximum, '%')" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
			<tbody>
				<tr>
					<th class="yellow smaller" colspan="2">
						<xsl:choose>
							<xsl:when test="saa:spellbook-reference">
								<xsl:for-each select="saa:spellbook-reference">
									<p><xsl:value-of select="@name"/></p>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>
								<p>-</p>
							</xsl:otherwise>
						</xsl:choose>
					</th>
				</tr>
			</tbody>
		</table>
	</xsl:template>
	
	<xsl:template match="saa:class" mode="experience">
		<xsl:variable name="class" select="."/>
		<table class="ClassData">
			<caption><xsl:value-of select="@name"/></caption>
			<thead>
				<tr>
					<th>level</th>
					<th>experience</th>
					<th>hp</th>
					<th>sp</th>
					<th>tp</th>
					<th>slp</th>
				</tr>
			</thead>
			<tbody>
				<xsl:for-each select="sfx:range(1, 50)">
					<xsl:variable name="lvl" select="."/>
					<tr class="right-aligned">
						<td class="number green">
							<xsl:value-of select="$lvl" />
						</td>
						<td class="number yellow">
							<xsl:value-of select="$class/@base-experience * $lvl * ($lvl + 1) div 2" />
						</td>
						<td class="number">
							<xsl:value-of select="$class/@hp-per-level * $lvl" />
						</td>
						<td class="number">
							<xsl:value-of select="$class/@sp-per-level * $lvl" />
						</td>
						<td class="number">
							<xsl:value-of select="$class/@tp-per-level * $lvl" />
						</td>
						<td class="number">
							<xsl:value-of select="$class/@slp-per-level * $lvl" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>

	<xsl:template match="saa:class-instance" mode="itemlist-inline">
		<table class="ClassData">
			<tbody>
				<xsl:for-each select="saa:skill">
					<tr class="right-aligned">
						<td>
							<xsl:value-of select="@name" />
							<xsl:text>:</xsl:text>
						</td>
						<td class="number">
							<xsl:value-of select="concat(@current, '%')" />
						</td>
						<td>
							<xsl:text>/</xsl:text>
						</td>
						<td class="number">
							<xsl:value-of select="concat(@maximum, '%')" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>

	<xsl:template match="saa:race" mode="itemlist">
		<article data-race-id="{@id}" data-template="flex" class="Race">
			<xsl:value-of select="@name" />
			<xsl:apply-templates select="." mode="itemlist-inline" />
		</article>
	</xsl:template>

	<xsl:template match="saa:race" mode="itemlist-inline">
		<table class="RaceData">
			<tbody>
				<xsl:for-each select="saa:attribute">
					<tr class="right-aligned">
						<td>
							<xsl:value-of select="@name" />
							<xsl:text>:</xsl:text>
						</td>
						<xsl:if test="@current">
							<td class="number">
								<xsl:value-of select="concat(format-number(@current, '###'), '')" />
							</td>
							<td>
								<xsl:text>/</xsl:text>
							</td>
						</xsl:if>
						<td class="number">
							<xsl:value-of select="concat(@maximum, '')" />
						</td>
					</tr>
				</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>

	<xsl:template match="saa:portrait" mode="itemlist">
		<article data-portrait-id="{@id}" data-template="flex" class="Portrait">
			<amber-picker infoset="lib.portraits" type="portrait" class="portrait-picker" role="button" tabindex="0">
				<amber-portrait-id value="{@id}" />
			</amber-picker>
			<xsl:value-of select="@name" />
		</article>
	</xsl:template>

	<xsl:template match="saa:monster | saa:npc | saa:pc" mode="itemlist">
		<xsl:variable name="isMage" select="saa:class-instance/saa:sp/@maximum &gt; 0"/>
		<article data-monster-id="{@id}" class="Character">
			<table>
				<tr>
					<td class="attributes">
						<h3>attributes</h3>
						<xsl:apply-templates select="saa:race" mode="itemlist-inline" />
					</td>
					<td class="languages">
						<h3>languages</h3>
						<xsl:variable name="languages" select="saa:language/@name"/>
						<table >
							<xsl:for-each select="key('dictionary-option', 'languages')">
								<tr>
									<td>
										<xsl:if test="@val = $languages">
											<xsl:value-of select="@val" />
										</xsl:if>
										<xsl:text>&#160;</xsl:text>
									</td>
								</tr>
							</xsl:for-each>
						</table>
					</td>
					<td class="skills">
						<h3>skills</h3>
						<xsl:apply-templates select="saa:class-instance" mode="itemlist-inline" />
					</td>
					<td class="character-sheet">
						<table>
							<tr>
								<td rowspan="5">
									<xsl:apply-templates select="." mode="itemlist-picture"/>
								</td>
								<td><xsl:value-of select="saa:race/@name"/></td>
							</tr>
							<tr><td><xsl:value-of select="concat(@gender, ' ')"/></td></tr>
							<tr><td>age: <xsl:value-of select="saa:race/saa:age/@current"/></td></tr>
							<tr><td><xsl:value-of select="saa:class-instance/@name"/><xsl:text> </xsl:text><xsl:value-of select="@level"/></td></tr>
							<tr><td>ep: <xsl:value-of select="saa:class-instance/@experience"/></td></tr>
						</table>
						<table>
							<tr>
								<td colspan="2" class="yellow">
									<xsl:value-of select="@name" />
								</td>
							</tr>
							<tr>
								<td colspan="2">hp: <xsl:value-of select="concat(saa:class-instance/saa:hp/@current, '/', saa:class-instance/saa:hp/@maximum)"/></td>
							</tr>
							<tr>
								<td colspan="2">
									<xsl:if test="$isMage">
										sp: <xsl:value-of select="concat(saa:class-instance/saa:sp/@current, '/', saa:class-instance/saa:sp/@maximum)"/>
									</xsl:if>
								</td>
							</tr>
							<tr>
								<td>
									<xsl:if test="$isMage">
										slp: <xsl:value-of select="@spelllearn-points"/>
									</xsl:if>
								</td>
								<td>
									tp: <xsl:value-of select="@training-points"/>
								</td>
							</tr>
							<tr>
								<td>
									gold: <xsl:value-of select="@gold"/>
								</td>
								<td>
									food: <xsl:value-of select="@food"/>
								</td>
							</tr>
							<tr>
								<td>
									attack: <xsl:value-of select="@attack"/>
								</td>
								<td>
									defense: <xsl:value-of select="@defense"/>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<xsl:if test="saa:equipment and saa:inventory">
				<table>
					<tr>
						<td>
							<h3>equipment</h3>
							<xsl:apply-templates select="saa:equipment" mode="itemlist-inline" />
						</td>
						<td>
							<h3>inventory</h3>
							<xsl:apply-templates select="saa:inventory" mode="itemlist-inline" />
							<xsl:if test="saa:class-instance/saa:spellbook-instance/*">
								<h3>spells</h3>
								<xsl:apply-templates select="saa:class-instance/saa:spellbook-instance[*]" mode="itemlist-inline" />
							</xsl:if>
						</td>
					</tr>
				</table>
			</xsl:if>
			<xsl:if test="saa:event">
				<div>
					<h3>dialog</h3>
					<xsl:for-each select="saa:event">
						<details>
							<summary class="textlabel">
								<xsl:for-each select="saa:trigger">
									<xsl:value-of select="@name"/>
									<xsl:if test="@value">
										<xsl:text>: </xsl:text>
										<span class="yellow"><xsl:value-of select="@value"/></span>
									</xsl:if>
								</xsl:for-each>
							</summary>
							<xsl:for-each select="saa:text">
								<div class="textbox">
									<xsl:copy-of select="node()" />
								</div>
							</xsl:for-each>
						</details>
					</xsl:for-each>
				</div>
			</xsl:if>
		</article>
	</xsl:template>
	
	<xsl:template match="saa:monster" mode="itemlist-picture">
		<amber-picker infoset="lib.monsters" type="monster" class="item-picker" role="button" tabindex="0">
			<amber-monster-id value="{@id}" />
		</amber-picker>
	</xsl:template>
	
	<xsl:template match="saa:pc | saa:npc" mode="itemlist-picture">
		<amber-picker infoset="lib.portraits" type="portrait" class="item-picker" role="button" tabindex="0">
			<amber-portrait-id value="{@portrait-id}" />
		</amber-picker>
	</xsl:template>
	
	<xsl:template match="saa:npc2 | saa:p2c" mode="itemlist">
		<!--<item xmlns="" id="361" image="9" name="MAGIERSTIEFEL" type="Schuhe" hands="0" fingers="0" damage="0" armor="6" weight="850" 
			gender="beide" class="Magier Mystik. Alchem. Heiler"/> -->
		<article data-npc-id="{@id}" data-template="flex">
			<ul>
				<li>
					<h3 class="yellow">
						<xsl:value-of select="@name" />
					</h3>
					<amber-picker infoset="lib.portraits" type="portrait" class="item-picker" role="button" tabindex="0">
						<amber-portrait-id value="{@portrait-id}" />
					</amber-picker>
				</li>
				<li>
					<h3>race: <xsl:value-of select="saa:race/@name"/></h3>
					<xsl:apply-templates select="saa:race" mode="itemlist-inline" />
				</li>
				<li>
					<h3>class: <xsl:value-of select="saa:class-instance/@name"/></h3>
					<xsl:apply-templates select="saa:class-instance" mode="itemlist-inline" />
				</li>
				<li>
					
				</li>
			</ul>
		</article>
	</xsl:template>
	<xsl:template match="saa:spellbook-instance" mode="itemlist-inline">
		<div class="spells">
			<h3 class="yellow"><xsl:value-of select="@name"/></h3>
			<ul>
				<xsl:for-each select="saa:spell-reference">
					<li>
						<xsl:value-of select="@name" />
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="saa:equipment" mode="itemlist-inline">
		<xsl:variable name="options" select="key('dictionary-option', 'equipment-slots')" />
		<div class="equipment" data-template="flex">
			<ul>
				<xsl:for-each select="saa:slot">
					<xsl:variable name="pos" select="position()"/>
					<li>
						<xsl:apply-templates select="." mode="itemlist-inline" />
						<span class="name"><xsl:value-of select="$options[$pos]/@val"/></span>
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="saa:inventory" mode="itemlist-inline">
		<div class="inventory" data-template="flex">
			<ul>
				<xsl:for-each select="saa:slot">
					<li>
						<xsl:apply-templates select="." mode="itemlist-inline" />
					</li>
				</xsl:for-each>
			</ul>
		</div>
	</xsl:template>

	<xsl:template match="saa:slot" mode="itemlist-inline">
		<amber-picker infoset="lib.items" type="item" class="item-picker" role="button" tabindex="0"
			onclick="savegameEditor.openPopup(arguments[0])">
			<amber-item-id value="{saa:item-instance/@item-id}" />
			<amber-item-amount value="{saa:item-instance/@item-amount}" />
			<amber-item-charge value="{saa:item-instance/@item-charge}" />
		</amber-picker>
		<xsl:if test="@name">
			<span class="name">
				<xsl:value-of select="@name" />
			</span>
		</xsl:if>
	</xsl:template>

	<xsl:template match="saa:item" mode="itemlist">
		<!--<item xmlns="" id="361" image="9" name="MAGIERSTIEFEL" type="Schuhe" hands="0" fingers="0" damage="0" armor="6" weight="850" 
			gender="beide" class="Magier Mystik. Alchem. Heiler"/> -->
		<article data-item-id="{@id}" data-template="flex" class="Item">
			<ul>
				<li>
					<table class="ItemName">
						<tr>
							<td>
								<amber-picker infoset="lib.items" type="item" class="item-picker" role="button" tabindex="0">
									<amber-item-gfx value="{@image-id}" />
								</amber-picker>
							</td>
							<td>
								<h3>
									<xsl:value-of select="@name" />
								</h3>
								<xsl:value-of select="@type" />
							</td>
						</tr>
					</table>
					<table class="ItemData">
						<tbody>
							<tr class="right-aligned">
								<td>Gewicht:</td>
								<td class="number">
									<xsl:value-of select="concat(@weight, ' gr')" />
								</td>
							</tr>
							<tr class="right-aligned">
								<td>Wert:</td>
								<td class="number">
									<xsl:value-of select="concat(@price, ' gp')" />
								</td>
							</tr>
						</tbody>
						<tbody>
							<tr class="right-aligned">
								<td>Hände:</td>
								<td class="number">
									<xsl:value-of select="@hands" />
								</td>
							</tr>
							<tr class="right-aligned">
								<td>Finger:</td>
								<td class="number">
									<xsl:value-of select="@fingers" />
								</td>
							</tr>
							<tr class="right-aligned">
								<td>Schaden:</td>
								<td class="number">
									<xsl:if test="@damage &gt; 0">
										<xsl:text>+</xsl:text>
									</xsl:if>
									<xsl:value-of select="@damage" />
								</td>
							</tr>
							<tr class="right-aligned">
								<td>Schutz:</td>
								<td class="number">
									<xsl:if test="@armor &gt; 0">
										<xsl:text>+</xsl:text>
									</xsl:if>
									<xsl:value-of select="@armor" />
								</td>
							</tr>
						</tbody>
					</table>
				</li>
				<xsl:choose>
					<xsl:when test="saa:text">
						<li>
							<div class="textbox">
								<xsl:copy-of select="saa:text/node()" />
							</div>
						</li>
					</xsl:when>
					<xsl:otherwise>
						<li>
							<table class="ItemClasses">
								<tbody>
									<tr>
										<td class="gray">------ Klassen ------</td>
									</tr>
									<tr>
										<td>
											<ul>
												<xsl:for-each select="saa:class-reference/@name">
													<li>
														<xsl:value-of select="." />
													</li>
												</xsl:for-each>
											</ul>
										</td>
									</tr>
								</tbody>
								<tbody>
									<tr>
										<td class="gray">Geschlecht</td>
									</tr>
									<tr>
										<td>
											<xsl:value-of select="@gender" />
										</td>
									</tr>
								</tbody>
							</table>
						</li>
						<li>
							<table class="ItemMagic">
								<tbody>
									<tr>
										<td class="right-aligned" data-hover-text="Lebenspunkte Maximum">LP-Max: </td>
										<td class="number">
											<xsl:if test="@lp-max &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
											</xsl:if>
											<xsl:value-of select="@lp-max" />
										</td>
										<td class="right-aligned" data-hover-text="Spruchpunkte Maximum">SP-Max: </td>
										<td class="number">
											<xsl:if test="@sp-max &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
											</xsl:if>
											<xsl:value-of select="@sp-max" />
										</td>
									</tr>
									<tr>
										<td class="right-aligned" data-hover-text="Magischer Rüstschutz, Angriff">M-B-W: </td>
										<td class="number">
											<xsl:if test="@magic-weapon &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
											</xsl:if>
											<xsl:value-of select="@magic-weapon" />
										</td>
										<td class="right-aligned" data-hover-text="Magischer Rüstschutz, Verteidigung">M-B-R: </td>
										<td class="number">
											<xsl:if test="@magic-armor &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
											</xsl:if>
											<xsl:value-of select="@magic-armor" />
										</td>
									</tr>
								</tbody>
								<tbody>
									<tr>
										<td colspan="4" class="orange">
											<xsl:if test="@attribute-value &gt; 0">
												Attribut
											</xsl:if>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<xsl:if test="@attribute-value &gt; 0">
												<xsl:value-of select="@attribute-type" />
											</xsl:if>
										</td>
										<td class="number">
											<xsl:if test="@attribute-value &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
												<xsl:value-of select="@attribute-value" />
											</xsl:if>
										</td>
									</tr>
								</tbody>
								<tbody>
									<tr>
										<td colspan="4" class="orange">
											<xsl:if test="@skill-value &gt; 0">
												Fähigkeit
											</xsl:if>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<xsl:if test="@skill-value &gt; 0">
												<xsl:value-of select="@skill-type" />
											</xsl:if>
										</td>
										<td class="number">
											<xsl:if test="@skill-value &gt; 0">
												<xsl:choose>
													<xsl:when test="@is-cursed">
														-
													</xsl:when>
													<xsl:otherwise>
														+
													</xsl:otherwise>
												</xsl:choose>
												<xsl:value-of select="@skill-value" />
											</xsl:if>
										</td>
									</tr>
								</tbody>
								<tbody>
									<tr>
										<td colspan="4" class="orange">
											<xsl:if test="@spell-id &gt; 0">
												<xsl:value-of select="@spell-type" />
											</xsl:if>
										</td>
									</tr>
									<tr>
										<td colspan="4">
											<xsl:if test="@spell-id &gt; 0">
												<xsl:value-of select="concat(@spell-name, ' (', @charges-default, ')')" />
											</xsl:if>
											<xsl:if test="@is-cursed">
												<span class="green">verflucht</span>
											</xsl:if>
										</td>
									</tr>
								</tbody>
							</table>
						</li>
					</xsl:otherwise>
				</xsl:choose>
				<!--<xsl:copy-of select="."/> -->
			</ul>
		</article>
	</xsl:template>
</xsl:stylesheet>
