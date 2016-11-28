<?php
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPItemModel extends ExportAIPModel {
    
    public $XML;
    public $name_folder_item;
    
    /**
     * metodo que executa os demais para criacao do mets e do zip do repositorio
     */
    public function create_items() {
        $collections = $this->get_all_collections();
        foreach ($collections as $collection) {
            if($collection  && $collection->ID && $collection->ID == get_option('collection_root_id')){
                continue;
            }
            $items = $this->get_collection_posts($collection->ID);
            foreach ($items as $item) {
                $this->name_folder_item = 'ITEM@'.$this->prefix.'-'. $item->ID;
                $dir_item = $this->dir.'/'.$this->name_folder.'/'.$this->name_folder_item;
                $this->recursiveRemoveDirectory($dir_item);
                if(!is_dir($dir_item.'/')){
                     mkdir($dir_item);
                }
                $this->generate_xml(get_post($item->ID),$collection->ID);
                $this->create_xml_file($dir_item.'/mets.xml', $this->XML);
                $this->create_zip_by_folder($this->dir.'/'.$this->name_folder.'/', $this->name_folder_item.'/', $this->name_folder_item);
                $this->recursiveRemoveDirectory($dir_collection);
            }
            
        }
        
    }
    
    
    public function generate_xml(WP_Post $item,$collection_id){
        $this->XML = '<?xml version="1.0" encoding="utf-8" standalone="no"?>';
        $this->XML .= '<mets ID="DSpace_ITEM_'.$this->prefix.'-'.$item->ID.'" OBJID="hdl:'.$this->prefix.'/'.$item->ID.'" TYPE="DSpace ITEM" '
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
        $this->generate_xml_item($item,$collection_id);
        $this->getFileThumbnail($collection->ID);
        $this->generate_items_xml($collection->ID);
        $this->XML .= '</mets>';
    }
    
    /**
     *  gera o xml dos grupos do tainacan
     */
    public function generate_xml_item(WP_Post $item,$collection_id){
        $this->XML .= '
            <amdSec ID="amd_3">
                <rightsMD ID="rightsMD_9">
                    <mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                        <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd">
                        <rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                            <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                              <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                            </rights:Context>
                          </rights:RightsDeclarationMD>
                    </xmlData>
                </mdWrap>
               </rightsMD>
                <sourceMD ID="sourceMD_10">
                 <mdWrap MDTYPE="OTHER" OTHERMDTYPE="AIP-TECHMD">
                    <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim"><dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="ITEM">
                        <dim:field mdschema="dc" element="creator">'. get_user_by('id', $item->post_author)->user_email.'</dim:field>
                        <dim:field mdschema="dc" element="identifier" qualifier="uri">hdl:'.$this->prefix.'/'.$ITEM->ID.'</dim:field>
                        <dim:field mdschema="dc" element="relation" qualifier="isPartOf">hdl:'.$this->prefix.'/'.$collection_id.'</dim:field>
                      </dim:dim>
                    </xmlData>
                 </mdWrap>
                </sourceMD>
            </amdSec>'; 
        $this->XML .= '<amdSec ID="amd_13">
                        <rightsMD ID="rightsMD_19">
                         <mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                          <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd"><rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                        <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                          <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                        </rights:Context>
                      </rights:RightsDeclarationMD></xmlData>
                         </mdWrap>
                        </rightsMD>
                       </amdSec>';
                
    }
    
    /**
     * 
     */
    public function get_all_files($item_id) {
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' => null,
            'post_parent' => $item_id
        );
        return get_posts($args);
    }
    
    /**
     * busca no banco os usuario para cada role
     */
    public function get_premis_files(WP_Post $item) {
        $index = 22;
        $files = $this->get_all_files($item->ID);
        if($files){
            foreach ($files as $file) {
                $url_image = wp_get_attachment_url($file->ID);
                $size = filesize(get_attached_file($file->ID));
                $name = $file->post_title;
                $md5_inicial = get_post_meta($file->ID, 'md5_inicial', true);
                 $this->XML .= '<amdSec ID="amd_'.$index++.'">';
                 $this->XML .= '<techMD ID="techMD_'.$index++.'">';
                 $this->XML .= '<mdWrap MDTYPE="PREMIS">';
                 $this->XML .= '<xmlData xmlns:premis="http://www.loc.gov/standards/premis" xsi:schemaLocation="http://www.loc.gov/standards/premis http://www.loc.gov/standards/premis/PREMIS-v1-0.xsd">'
                                . '<premis:premis xmlns:premis="http://www.loc.gov/standards/premis">';
                 $this->XML .= '<premis:object>
                                    <premis:objectIdentifier>
                                      <premis:objectIdentifierType>URL</premis:objectIdentifierType>
                                      <premis:objectIdentifierValue>'.$url_image.'</premis:objectIdentifierValue>
                                    </premis:objectIdentifier>
                                    <premis:objectCategory>File</premis:objectCategory>
                                    <premis:objectCharacteristics>
                                      <premis:fixity>
                                        <premis:messageDigestAlgorithm>MD5</premis:messageDigestAlgorithm>
                                        <premis:messageDigest>'.$md5_inicial.'</premis:messageDigest>
                                      </premis:fixity>
                                      <premis:size>'.$size.'</premis:size>
                                      <premis:format>
                                        <premis:formatDesignation>
                                          <premis:formatName>'. get_post_mime_type($file->ID).'</premis:formatName>
                                        </premis:formatDesignation>
                                      </premis:format>
                                    </premis:objectCharacteristics>
                                    <premis:originalName>'.$name.'</premis:originalName>
                                </premis:object>';
                 $this->XML .=  '</premis:premis>'
                             . '</xmlData>';
                 $this->XML .= '</mdWrap>';
                 $this->XML .= '</techMD>';
                 $this->XML .= '<rightsMD ID="rightsMD_'.$index++.'">';
                 $this->XML .= '<mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                                    <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd">
                                    <rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                                  <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                                    <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                                  </rights:Context>
                                </rights:RightsDeclarationMD>
                                </xmlData>
                               </mdWrap>
                            ';
                 $this->XML .= '</rightsMD>';
                 $this->XML .= '<sourceMD ID="sourceMD_29">
                                    <mdWrap MDTYPE="OTHER" OTHERMDTYPE="AIP-TECHMD">
                                     <xmlData xmlns:dim="http://www.dspace.org/xmlns/dspace/dim">
                                     <dim:dim xmlns:dim="http://www.dspace.org/xmlns/dspace/dim" dspaceType="BITSTREAM">
                                   <dim:field mdschema="dc" element="title">v11n3a36.pdf</dim:field>
                                   <dim:field mdschema="dc" element="title" qualifier="alternative">'.$url_image.'</dim:field>
                                   <dim:field mdschema="dc" element="format" qualifier="mimetype">'. get_post_mime_type($file->ID).'</dim:field>
                                   <dim:field mdschema="dc" element="format" qualifier="supportlevel">KNOWN</dim:field>
                                   <dim:field mdschema="dc" element="format" qualifier="internal">false</dim:field>
                                 </dim:dim></xmlData>
                                    </mdWrap>
                                   </sourceMD>';
                 $this->XML .= '</amdSec>';
                 $this->XML .= '<amdSec ID="amd_'.$index++.'">
                        <rightsMD ID="rightsMD_'.$index++.'">
                         <mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRIGHTS">
                          <xmlData xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" xsi:schemaLocation="http://cosimo.stanford.edu/sdr/metsrights/ http://cosimo.stanford.edu/sdr/metsrights.xsd"><rights:RightsDeclarationMD xmlns:rights="http://cosimo.stanford.edu/sdr/metsrights/" RIGHTSCATEGORY="LICENSED">
                        <rights:Context in-effect="true" CONTEXTCLASS="GENERAL PUBLIC">
                          <rights:Permissions DISCOVER="true" DISPLAY="true" MODIFY="false" DELETE="false" />
                        </rights:Context>
                      </rights:RightsDeclarationMD></xmlData>
                         </mdWrap>
                        </rightsMD>
                       </amdSec>';
            }
        }
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
          copy($fullsize_path, $dir_community.'/thumbnail_'.$collection_id.'.'.$ext);
          $this->XML .= '<fileSec>
                        <fileGrp ADMID="amd_94" USE="THUMBNAIL">
                         <file ID="bitstream_1" MIMETYPE="image/'.$ext.'" SIZE="'.$size.'" CHECKSUM="'.$md5_inicial.'" CHECKSUMTYPE="MD5">
                          <FLocat LOCTYPE="URL" xlink:type="simple" xlink:href="thumbnail_'.$collection_id.'"/>
                         </file>
                        </fileGrp>
                       </fileSec>'; 
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
