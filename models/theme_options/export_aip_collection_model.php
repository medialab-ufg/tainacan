<?php
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPCollectionModel extends ExportAIPModel {
    
    public $XML;
    public $name_folder_collection;
    
    public function get_count_collections() {
        $index = 0;
        $collections = $this->get_all_collections();
        foreach ($collections as $collection) {
            if($collection  && $collection->ID && $collection->ID == get_option('collection_root_id')){
                continue;
            }
            $index++;
        }
        return $index;
    }
    /**
     * metodo que executa os demais para criacao do mets e do zip do repositorio
     */
    public function create_collections() {
        $collections = $this->get_all_collections();
        foreach ($collections as $collection) {
            if($collection  && $collection->ID && $collection->ID == get_option('collection_root_id')){
                continue;
            }
            $this->name_folder_collection = 'COLLECTION@'.$this->prefix.'-'. $collection->ID;
            $dir_collection = $this->dir.'/'.$this->name_folder.'/'.$this->name_folder_collection;
            $this->recursiveRemoveDirectory($dir_collection);
            if(!is_dir($dir_collection.'/')){
                 mkdir($dir_collection);
            }
            $this->generate_xml(get_post($collection->ID));
            $this->create_xml_file($dir_collection.'/mets.xml', $this->XML);
            $this->create_zip_by_folder($this->dir.'/'.$this->name_folder.'/', $this->name_folder_collection.'/', $this->name_folder_collection,true);
            $this->recursiveRemoveDirectory($dir_collection);
        }
        
    }
    
    
    public function generate_xml(WP_Post $collection){
        $this->XML = '<?xml version="1.0" encoding="utf-8" standalone="no"?>';
        $this->XML .= '<mets ID="DSpace_COLLECTION_'.$this->prefix.'-'.$collection->ID.'" OBJID="hdl:'.$this->prefix.'/'.$collection->ID.'" TYPE="DSpace COLLECTION" '
                . 'PROFILE="http://www.dspace.org/schema/aip/mets_aip_1_0.xsd" '
                . 'xmlns="http://www.loc.gov/METS/" '
                . 'xmlns:xlink="http://www.w3.org/1999/xlink" '
                . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '
                . 'xsi:schemaLocation="http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd">';
        $this->XML .= trim('<metsHdr>
                            <agent ROLE="CUSTODIAN" TYPE="OTHER" OTHERTYPE="DSpace Archive">
                                <name>ri/0</name>
                            </agent>
                            <agent ROLE="CREATOR" TYPE="OTHER" OTHERTYPE="DSpace Software">
                                <name>DSpace 5.5</name>
                            </agent>
                       </metsHdr>');
        $this->XML .= '<dmdSec ID="dmdSec_1">
                        <mdWrap MDTYPE="MODS">
                         <xmlData xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xlink="http://www.w3.org/1999/xlink" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                         <mods:mods xmlns:mods="http://www.loc.gov/mods/v3" 
                         xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                         xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-1.xsd">
                         <mods:note>'.$collection->post_content.'</mods:note>
                        <mods:abstract />
                        <mods:tableOfContents />
                        <mods:identifier type="uri">'. get_the_permalink($collection->ID).'</mods:identifier>
                        <mods:accessCondition type="useAndReproduction" />
                        <mods:titleInfo>
                          <mods:title>'.$collection->post_title.'</mods:title>
                        </mods:titleInfo>
                      </mods:mods></xmlData>
                        </mdWrap>
                       </dmdSec>
                      ';
        $this->XML .= '<dmdSec ID="dmdSec_2">
                            <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DIM">
                             <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="COLLECTION">
                                <dim:field mdschema="dc" element="description" >'.$collection->post_content.'</dim:field>
                                <dim:field mdschema="dc" element="description" qualifier="abstract" />
                                <dim:field mdschema="dc" element="description" qualifier="tableofcontents" />
                                <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:'.$this->prefix.'/'.$collection->ID.'</dim:field>
                                <dim:field mdschema="dc" element="rights" />
                                <dim:field mdschema="dc" element="title">'.$collection->post_title.'</dim:field>
                                </dim:dim>
                              </xmlData>
                            </mdWrap>
                        </dmdSec>
                      ';
        $this->generate_xml_groups($collection);
        $this->getFileThumbnail($collection->ID);
        $this->generate_items_xml($collection->ID);
        $this->XML .= '</mets>';
    }
    
    /**
     *  gera o xml dos grupos do tainacan
     */
    public function generate_xml_groups(WP_Post $collection){
        $this->XML .= '
            <amdSec ID="amd_3">
                <techMD ID="techMD_5">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="DSPACE-ROLES">
                  <xmlData xmlns:dsroles="http://www.dspace.org/xmlns/dspace/dspace-roles">
                    <DSpaceRoles>
                         <Groups>
                             <Group ID="'.$this->get_moderators_collection_id($collection->ID).'" TYPE="SUBMIT" Name="administrator_'.$collection->ID.'">'.
                                        $this->get_users_moderators($collection)
                            .'</Group>
                         </Groups>'; 
        $this->XML .= '</DSpaceRoles>
                   </xmlData>
                </mdWrap>
              </techMD>
              <rightsMD ID="rightsMD_9">
                    <mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                        <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd">
                        <rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                            <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                              <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                            </rights:Context>
                            <rights:Context CONTEXTCLASS="GENERAL PUBLIC" in-effect="true">
                                <rights:Permissions DELETE="false" MODIFY="false" DISPLAY="true" DISCOVER="true" OTHERPERMITTYPE="READ ITEM CONTENTS" OTHER="true"/>
                            </rights:Context>
                            <rights:Context CONTEXTCLASS="GENERAL PUBLIC" in-effect="true">
                                <rights:Permissions DELETE="false" MODIFY="false" DISPLAY="true" DISCOVER="true" OTHERPERMITTYPE="READ FILE CONTENTS" OTHER="true"/>
                            </rights:Context>
                            <rights:Context in-effect="true" CONTEXTCLASS="MANAGED GRP">
                              <rights:UserName USERTYPE="GROUP">administrator_'.$collection->ID.'</rights:UserName>
                              <rights:Permissions DISCOVER="true" DISPLAY="true" COPY="true" DUPLICATE="true" MODIFY="true" DELETE="true" PRINT="true" OTHER="true" OTHERPERMITTYPE="ADMIN" />
                            </rights:Context>
                          </rights:RightsDeclarationMD>
                    </xmlData>
                </mdWrap>
               </rightsMD>
                <sourceMD ID="sourceMD_10">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="AIP-TECHMD">
                    <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="COLLECTION">
                        <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:'.$this->prefix.'/'.$collection->ID.'</dim:field>
                        <dim:field mdschema="dc" element="relation" qualifier="isPartOf">'.$this->is_children_collection($collection->ID).'</dim:field>
                      </dim:dim>
                    </xmlData>
                 </mdWrap>
                </sourceMD>
            </amdSec>'; 
    }
    
    /**
     * busca no banco os usuario para cada role
     */
    public function get_users_moderators(WP_Post $community) {
        $valor = '';
        $blogusers = $this->get_moderators($community->ID);
        if($blogusers){
            $valor .= '<Members>';
            foreach ( $blogusers as $user ) {
                    $user = get_user_by('id', $user);
                    $valor .= '<Member ID="'.$user->ID.'" Name="' . esc_html( $user->user_email ) . '" />';
            }
            $valor .= '</Members>';
        }
        return $valor;
    }
    
    /**
     * metodo que retorna o pai desta colecao seja comunidade ou outra colecao
     * @param type $collection_id
     * @return type
     */
    public function is_children_collection($collection_id,$remove_hdl = false) {
        $meta = get_post_meta($collection_id, 'socialdb_collection_parent' , true);
        if((!$meta || $meta=='' || !is_numeric($meta))   ){
            return (!$remove_hdl) ? 'hdl:'.$this->prefix.'/'.get_option('collection_root_id') : $this->prefix.'/'.get_option('collection_root_id');
        }else{
            $collection = $this->get_collection_by_category_root($meta);
            $collection = (is_array($collection)) ? $collection[0] : $collection;
            return (!$remove_hdl) ?  'hdl:'.$this->prefix.'/'.$collection->ID : $this->prefix.'/'.$collection->ID;
        }
    }
    
    /**
     * 
     * @param type $param
     */
    public function getFileThumbnail($collection_id) {
        $thumbnail_id = get_post_thumbnail_id($collection_id);
        $dir_community = $this->dir.'/'.$this->name_folder.'/'.$this->name_folder_repository;
        if($thumbnail_id){
          $fullsize_path = get_attached_file( $thumbnail_id ); // Full path
          $md5_inicial = get_post_meta($thumbnail_id, 'md5_inicial', true);
          $size = filesize(get_attached_file($thumbnail_id));
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          if($fullsize_path){
          copy($fullsize_path, $dir_community.'/thumbnail_'.$collection_id.'.'.$ext);
          $this->XML .= '<fileSec>
                        <fileGrp USE="LOGO">
                         <file ID="logo_25" MIMETYPE="image/'.$ext.'" SIZE="'.$size.'" CHECKSUM="'.$md5_inicial.'" CHECKSUMTYPE="MD5">
                          <FLocat LOCTYPE="URL" xlink:type="simple" xlink:href="thumbnail_'.$collection_id.'"/>
                         </file>
                        </fileGrp>
                       </fileSec>'; 
          }
        }
    }
    /**
     * metodo que cria a estrutura das comunidades
     */
    public function generate_items_xml($community_id) {
        $items = $this->get_collection_posts($community_id);
        $thumbnail_id = get_post_thumbnail_id($community_id);
        $this->XML .= '<structMap ID="struct_11" LABEL="DSpace Object" TYPE="LOGICAL">';
        $this->XML .= '<div ID="div_12" DMDID="dmdSec_2 dmdSec_1" ADMID="amd_3" TYPE="DSpace Object Contents">';
        $this->XML .=  ($thumbnail_id) ? '<fptr FILEID="logo_25"/>' : '';
        $index = 13;
        foreach ($items as $item):
        $this->XML .= '<div ID="div_'.$index++.'" TYPE="DSpace ITEM">';
        $this->XML .= '<mptr ID="mptr_'.$index++.'" LOCTYPE="HANDLE" xlink:type="simple" xlink:href="'.$this->prefix.'/'. $item->ID.'"/>';
        $this->XML .= '<mptr ID="mptr_'.$index++.'" LOCTYPE="URL" xlink:type="simple" xlink:href="ITEM@'.$this->prefix.'-'. $item->ID.'.zip"/>';
        $this->XML .= '</div>';
        endforeach;
        $this->XML .= '</div>';
        $this->XML .= '</structMap>';
        $this->XML .= '<structMap ID="struct_'.$index++.'" LABEL="Parent" TYPE="LOGICAL">';
        $this->XML .= '<div ID="div_'.$index++.'" LABEL="Parent of this DSpace Object" TYPE="AIP Parent Link">';
        $this->XML .= '<mptr ID="mptr_'.$index++.'" LOCTYPE="HANDLE" xlink:type="simple" xlink:href="'.$this->is_children_collection($community_id, true).'"/>';
        $this->XML .= '</div>';
        $this->XML .= '</structMap>';
    }
    
    /**
     * metodo que busca os meradores de uma colecao
     * @param int $id
     */
    public function get_moderators($id) {
        $moderators_array = [];
        $owner = get_post($id)->post_author;
        $moderators = get_post_meta($id, 'socialdb_collection_moderator');
        if(is_array($moderators)){
            $moderators_array = array_unique(array_filter($moderators));
        }
        $moderators_array[] = $owner;
        return $moderators_array;
    }
    
}
