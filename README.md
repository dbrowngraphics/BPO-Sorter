# BPO Summary Report Sorter

## New Procedure 
```
v3p_cigna_pbm_sort. the table is V3T_CIGNA_SORT
```



## Installation

### Server
```
sudo apt-get update
sudo apt-get install tesseract-ocr
sudo apt-get install imagemagick

Must change policy.xml file, to give PDF 'read | write' permissions.
Copy this command:
cd ../../etc/ImageMagick-6/

Then:
**Optional**sudo cp policy.xml [whatever name you want new file to be]
sudo chmod 777 policy.xml
vim policy.xml

https://stackoverflow.com/questions/52703123/override-default-imagemagick-policy-xml
```

### App Directory:

#### OCR
`composer require thiagoalessio/tesseract_ocr`

#### XLSX
* Only needed until we get SQL results from the DB
`composer require phpoffice/phpspreadsheet:dev-develop`

#### FPDI

Testing - WORKS!!! uses TCPDF as included dependency
https://www.setasign.com/products/fpdi/about

`composer require setasign/fpdi`
`composer require setasign/fpdi-tcpdf`

Used to extract pages from the source PDF


## Dependencies

[Tesseract is an open source Optical Character Recognition (OCR) Engine](https://github.com/tesseract-ocr/tesseract/wiki)

[A wrapper to work with Tesseract OCR inside PHP.](https://github.com/thiagoalessio/tesseract-ocr-for-php)


## Use

Make sure you have drop, process, and process/archive folders.
If not, create them.

Remove the vendor\phpoffice\phpspreadsheet folder.
Run composer install from the root.

Put a source PDF and a lookup XLS file in the drop folder.

Hitting the sort.php URL will start the magic process.


### Process

#### Archive

Move the source PDF and lookup XLS file to an archive folder - timestamped

#### Convert

Using the CLI tool 'convert', make JPG images of each page from the source PDF.

#### Lookup

Load the XLS file ready to use the Group Id, Group Name and NODE for sorting.

#### Sort

Each image created by the convert process will be OCR'ed and scanned for a matching Group Ip in the look dictionary/array.

When found it will be moved into the matching NODE folder.

If its Group Id is not found in the lookup dictionary/array, it will be moved into an 'unknown' folder.

#### Pdf

Page numbers will be extracted from the file names created in the sorting process.
These numbers are used to 'slice' pages from the source PDF to create new PDFs per NODE.

The unknown folder will be process creating a single PDF per file.

#### Zip

The resulting zip of TPA PDFs will be created in the Archive - timestamp folder.

#### Cleanup

All temp folders/files will be removed.

### Folders

#### Drop

Add the source PDF here.
Add the lookup XLS file here.

Generate the XLS file from Oracle:
```
select distinct
       a.node,
       a.cigna_group_id,
       b.group_name,
       a.group_id
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and a.group_id = b.group_id
order by a.cigna_group_id
```
Note: when exporting, you'll want to rename the exported file (Excel) from xlsx to xls. Not sure why the export incorrectly uses the xlsx extension.

From SQL:
```
select distinct
       a.node,
       a.cigna_group_id,
       b.group_name,
       a.group_id
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and a.group_id = b.group_id
where (a.group_term_date is null or a.group_term_date >= trunc(sysdate))
   and a.pbm_plan_id is not null
order by a.cigna_group_id
```

Include Termed
```
select distinct
       a.node,
       a.cigna_group_id,
       b.group_name,
       a.group_id
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and a.group_id = b.group_id
where a.pbm_plan_id is not null
order by a.cigna_group_id
```

Including PBM (currently using this)
***************************************
using this one
***************************************
```
select distinct
       a.node,
       a.cigna_group_id,
       b.group_name,
       a.group_id
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and a.group_id = b.group_id
order by a.cigna_group_id
```

Reduced set from above
```
select distinct
       a.node,
       a.cigna_group_id,
       b.group_name
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and b.group_id = '*'
order by a.cigna_group_id
```

Everything related to that group_id
```
select distinct
       a.node,
       (select group_name from b_group c where c.node = a.node and group_id = '*') group_name,
        b.group_name division_name,
       a.cigna_group_id,
       a.group_id,
       a.subgroup
  from r_cigna_ng_include a
  left join b_group b
    on a.node = b.node
   and b.group_id = a.group_id
order by a.node, a.cigna_group_id
```

#### Process

The source PDF is copied here: source.pdf
The lookup XLS is copied here: lookup.xls

Source, target, pdfs folders are all created and used through the process until the PDF zip is create.
these folders and contents will be removed/deleted afterward.


##### Archive

Each run of this utility will backup the source PDF and lookup SLX here.
The PDF zip file will be created here.
