# Lists Elements API Methods - Technical Summary

## Service Overview

The **Lists Elements** service provides API methods for managing elements within universal lists in Bitrix24. This service operates within the **`lists`** scope and provides complete CRUD functionality for list elements.

**Scope**: `lists`

**Base API Path**: `lists.element.*`

## Connection Dependencies

The Elements service connects with multiple Bitrix24 entities:

- **Lists**: Elements belong to lists (use `lists.get` to obtain list identifiers)
- **Sections**: Elements can be placed in sections (use `lists.section.get` for section IDs)
- **Fields**: Custom properties (use `lists.field.get` for field metadata)
- **CRM Objects**: Links to leads, deals, contacts, companies, SPAs
- **Users**: Employee links, creation/modification tracking
- **Files**: Drive integration for file attachments

## Common Parameters

All methods share these common identification parameters:

### Information Block Identification (Required)
- `IBLOCK_TYPE_ID` (string, required): Type identifier
  - `"lists"` - list information block type
  - `"bitrix_processes"` - processes information block type  
  - `"lists_socnet"` - group lists information block type
- At least one of:
  - `IBLOCK_ID` (integer): Information block identifier (from `lists.get`)
  - `IBLOCK_CODE` (string): Information block symbolic code (from `lists.get`)

### Element Identification (Where Applicable)
- At least one of:
  - `ELEMENT_ID` (integer): Element identifier
  - `ELEMENT_CODE` (string): Element symbolic code

## API Methods

### 1. lists.element.add

**Purpose**: Creates a new list element

**Documentation URL**: https://apidocs.bitrix24.com/api-reference/lists/elements/lists-element-add.html

**Access Permission**: User with "Add" or "Edit" access permission for the required list

**Parameters**:
- Common information block parameters (required)
- `ELEMENT_CODE` (string, required): Symbolic code of the element
- `FIELDS` (array, required): Array of fields - see Fields Parameter section
- `IBLOCK_SECTION_ID` (integer, optional): Section identifier (default: 0 - root level)
- `LIST_ELEMENT_URL` (string, optional): Template address with replacements (#list_id#, #section_id#, #element_id#, #group_id#)

**Fields Parameter Structure**:
- `NAME` (string, required): Name of the element
- `PROPERTY_PropertyId`: Custom properties
  - Multiple properties: pass as array even for single values
  - File type: base64 or array with name and base64
  - File (Drive) type: file identifier from Drive

**Return Values**:
- `result` (integer): ID of the created element
- `time` (object): Request execution time information

**Error Codes**:
- `ERROR_REQUIRED_PARAMETERS_MISSING`: Required parameter missing
- `ERROR_IBLOCK_NOT_FOUND`: Information block not found
- `ERROR_ELEMENT_ALREADY_EXISTS`: Element with this CODE already exists
- `ERROR_ADD_ELEMENT`: Error adding element
- `ERROR_ELEMENT_FIELD_VALUE`: Field value validation error
- `ACCESS_DENIED`: Insufficient permissions

### 2. lists.element.update

**Purpose**: Updates an existing list element

**Documentation URL**: https://apidocs.bitrix24.com/api-reference/lists/elements/lists-element-update.html

**Access Permission**: User with "Edit" permission for the required list

**Important**: Method completely overwrites the element. Fields not provided will be cleared.

**Parameters**:
- Common information block parameters (required)
- Common element identification parameters (required)
- `FIELDS` (array, required): Array of fields to update

**Fields Parameter**: Same structure as `add` method

**Return Values**:
- `result` (boolean): `true` if successfully updated
- `time` (object): Request execution time information

**Error Codes**:
- `ERROR_REQUIRED_PARAMETERS_MISSING`: Required parameter missing
- `ERROR_IBLOCK_NOT_FOUND`: Information block not found
- `ERROR_ELEMENT_NOT_FOUND`: Element with such ID/CODE not found
- `ERROR_UPDATE_ELEMENT`: Error updating element
- `ERROR_ELEMENT_FIELD_VALUE`: Field value validation error
- `ACCESS_DENIED`: Insufficient permissions

### 3. lists.element.get

**Purpose**: Returns element data or list of elements

**Documentation URL**: https://apidocs.bitrix24.com/api-reference/lists/elements/lists-element-get.html

**Access Permission**: User with "Read" access permission for the required list

**Parameters**:
- Common information block parameters (required)
- Common element identification parameters (optional - for single element)
- `SELECT` (array, optional): Fields to select (default: all fields)
- `FILTER` (object, optional): Filtering conditions
- `ELEMENT_ORDER` (object, optional): Sorting configuration
- `start` (integer, optional): Pagination offset (50 records per page)

**Available SELECT Fields**:
- `ID`: Element identifier
- `CODE`: Element code
- `NAME`: Element name
- `IBLOCK_SECTION_ID`: Section identifier
- `CREATED_BY`: Creator user ID
- `CREATED_USER_NAME`: Creator name (deprecated)
- `ACTIVE_TO`: Activity end date (deprecated)
- `BP_PUBLISHED`: Workflow publication (deprecated)
- `DATE_CREATE`: Creation date
- `PREVIEW_TEXT`: Preview text (deprecated)
- `DETAIL_TEXT`: Detail text (deprecated)
- `SORT`: Sorting value
- `PREVIEW_TEXT_TYPE`: Preview text type (deprecated)
- `DETAIL_TEXT_TYPE`: Detail text type (deprecated)
- `PROPERTY_PropertyId`: Custom properties

**Filter Options**:
- Basic fields: `NAME`, `IBLOCK_SECTION_ID`, `CREATED_BY`, `DATE_CREATE`, `SORT`, etc.
- Custom properties: `PROPERTY_PropertyId`
- Filter prefixes: `>=`, `>`, `<=`, `<`, `=`, `%` (LIKE), `!` (NOT)

**Sorting Options**:
- Direction: `asc` (ascending), `desc` (descending)
- **Limitations**: No multiple property sorting, no sorting for Money, PREVIEW_TEXT, DETAIL_TEXT, ECrm, DiskFile, IBLOCK_SECTION_ID types

**Return Values**:
- `result` (array): Element data or array of elements
- `total` (integer): Total number of elements
- `time` (object): Request execution time information

**Error Codes**:
- `ERROR_REQUIRED_PARAMETERS_MISSING`: Required parameter missing
- `ERROR_IBLOCK_NOT_FOUND`: Information block not found
- `ACCESS_DENIED`: Insufficient permissions

### 4. lists.element.delete

**Purpose**: Deletes a list element

**Documentation URL**: https://apidocs.bitrix24.com/api-reference/lists/elements/lists-element-delete.html

**Access Permission**: User with "Edit" access permission for the required list

**Important**: Files from "File (Drive)" fields are removed from drive only if not used elsewhere

**Parameters**:
- Common information block parameters (required)
- Common element identification parameters (required)

**Return Values**:
- `result` (boolean): `true` if successfully deleted
- `time` (object): Request execution time information

**Error Codes**:
- `ERROR_REQUIRED_PARAMETERS_MISSING`: Required parameter missing
- `ERROR_IBLOCK_NOT_FOUND`: Information block not found
- `ERROR_ELEMENT_NOT_FOUND`: Element with such ID/CODE not found
- `ERROR_DELETE_ELEMENT`: Error deleting element
- `ACCESS_DENIED`: Insufficient permissions

### 5. lists.element.get.file.url

**Purpose**: Returns file download paths for File or File (Drive) properties

**Documentation URL**: https://apidocs.bitrix24.com/api-reference/lists/elements/lists-element-get-file-url.html

**Access Permission**: User with "Read" access permission for the required list

**Parameters**:
- Common information block parameters (required)
- Common element identification parameters (required)
- `FIELD_ID` (integer, required): File property identifier (without PROPERTY_ prefix)

**Return Values**:
- `result` (array): Array of download links (empty array if no files)
- `time` (object): Request execution time information

**Response Examples**:
- File (Drive): `["/bitrix/tools/disk/uf.php?attachedId=103&action=download&ncc=1"]`
- File: `["/company/lists/37/file/0/6651/PROPERTY_425/32521/?ncc=y&download=y"]`

**Error Codes**:
- `ERROR_REQUIRED_PARAMETERS_MISSING`: Required parameter missing
- `ERROR_IBLOCK_NOT_FOUND`: Information block not found
- `ERROR_ELEMENT_NOT_FOUND`: Element with such ID/CODE not found
- `ACCESS_DENIED`: Insufficient permissions

## Implementation Guidelines

### Service Architecture

Following the SDK patterns, implement:

```
src/Services/Lists/Element/
├── Service/
│   ├── Element.php          # Main service
│   └── Batch.php           # Batch operations service
└── Result/
    ├── ElementResult.php        # Single element result (add)
    ├── ElementsResult.php       # Multiple elements result (get list)
    ├── ElementItemResult.php    # Individual element DTO
    ├── UpdatedElementResult.php # Update operation result
    ├── DeletedElementResult.php # Delete operation result
    └── FileUrlsResult.php       # File URLs result
```

### Method Implementation Patterns

#### Standard CRUD Methods
- `add(string $iblockTypeId, int|string $iblock, string $elementCode, array $fields, ?int $sectionId = null, ?string $listElementUrl = null): ElementResult`
- `update(string $iblockTypeId, int|string $iblock, int|string $element, array $fields): UpdatedElementResult`
- `get(string $iblockTypeId, int|string $iblock, ?int|string $element = null, array $select = [], array $filter = [], array $order = [], int $start = 0): ElementsResult`
- `delete(string $iblockTypeId, int|string $iblock, int|string $element): DeletedElementResult`
- `getFileUrl(string $iblockTypeId, int|string $iblock, int|string $element, int $fieldId): FileUrlsResult`

#### Batch Operations
Implement batch counterparts for mass operations:
- `list()` - generator for traversable large datasets
- Potential for batch add/update/delete operations

### Result Classes Design

#### ElementItemResult Properties
Based on available fields, implement lazy-loaded properties:

```php
/**
 * @property-read int $id
 * @property-read non-empty-string $code
 * @property-read non-empty-string $name
 * @property-read ?int $iblockSectionId
 * @property-read int $createdBy
 * @property-read CarbonImmutable $dateCreate
 * @property-read int $sort
 * @property-read array $properties  // Dynamic custom properties
 */
class ElementItemResult extends AbstractItem
{
    // Implement lazy loading with type conversion
}
```

### Integration Testing Strategy

#### Test Scenarios
1. **Full CRUD cycle**: Create → Read → Update → Read → Delete
2. **Field types testing**: Text, Number, Date, File, File (Drive), Link fields
3. **Permission testing**: Different access levels
4. **Section operations**: Elements in sections vs root level
5. **File operations**: Upload, attach, retrieve URLs, cleanup
6. **Filtering and sorting**: Various filter conditions and sort orders
7. **Pagination**: Large datasets with start parameter
8. **Error handling**: Invalid parameters, missing permissions, non-existent entities

#### Test Data Requirements
- Test list with various field types
- Test sections within the list
- Sample files for File and Drive properties
- User accounts with different permission levels

### Performance Considerations

#### Pagination
- Fixed 50 records per page
- Use `start` parameter for navigation: `start = (page - 1) * 50`

#### Batch Operations
- Implement generator-based list traversal for large datasets
- Consider batch operations for mass element manipulation

#### Field Selection
- Use `SELECT` parameter to limit returned fields
- Particularly important for elements with many custom properties

### Security Notes

#### Access Control
- Methods respect list-level permissions (Add/Edit/Read)
- Element access controlled by list permissions
- File access follows Drive permissions for File (Drive) fields

#### Input Validation
- Validate field values according to field types
- Handle file uploads securely through Drive API
- Sanitize filter conditions to prevent injection

### Special Considerations

#### File Handling
- File fields: Use base64 encoding or file arrays
- File (Drive) fields: Use Drive file identifiers
- File deletion: Automatic cleanup if not used elsewhere
- URL retrieval: Separate method for file download links

#### Field Types Support
- Standard fields: Text, Number, Date, etc.
- Complex fields: Money (with limitations), CRM links, Employee links
- Multiple values: Array format even for single values

#### Backward Compatibility
- Some fields marked as deprecated but still functional
- Modern alternatives should be preferred in new implementations

This comprehensive summary provides all necessary information for implementing a robust Lists Elements service within the Bitrix24 PHP SDK architecture.