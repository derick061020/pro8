### Compra

```json
{
  "id": "d399ce7a-12bd-48ce-83a7-bd5739f9006a",
  "data": {
    "id": 13,
    "total": 118,
    "number": "F001-3456",
    "supplier": {
      "name": "provee",
      "number": "20123321123",
      "identity_document_type_id": "6"
    },
    "external_id": "1761a677-5a20-4e8f-9e01-f4f1ad75ce61",
    "items_count": 0,
    "date_of_issue": "2026-06-12",
    "state_type_id": "01",
    "currency_type_id": "PEN",
    "document_type_id": "01",
    "exchange_rate_sale": 1
  },
  "event": "purchase.created",
  "created_at": "2026-06-12T13:52:01-05:00",
  "api_version": "1.0"
}
```

### venta creada

```json
{
  "id": "b8d08e65-4f61-4a95-85ac-e0e86256cb54",
  "data": {
    "id": 168,
    "qr": "iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAI...",
    "hash": null,
    "links": {
      "cdr": null,
      "pdf": "http://1.facturaloperu-pro8.oo/downloads/document/pdf/05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8",
      "xml": "http://1.facturaloperu-pro8.oo/downloads/document/xml/05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8"
    },
    "total": 50,
    "number": "F001-63",
    "customer": {
      "name": "EMPRESA XYZ S.A.",
      "number": "10414711225",
      "identity_document_type_id": "6"
    },
    "filename": "20515809822-01-F001-63",
    "external_id": "05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8",
    "date_of_issue": "2026-06-12",
    "state_type_id": "01",
    "sunat_response": null,
    "currency_type_id": "PEN",
    "document_type_id": "01",
    "number_to_letter": "Cincuenta  con 00/100 ",
    "exchange_rate_sale": 1,
    "state_type_description": "Registrado"
  },
  "event": "document.created",
  "created_at": "2026-06-12T13:46:44-05:00",
  "api_version": "1.0"
}
```

### venta - factura aceptada

```json
{
  "id": "7b5497c3-17d8-4dd1-9c36-e31f84699b21",
  "data": {
    "id": 168,
    "qr": "iVBORw0KGgoAAAANSUhEUgAA...",
    "hash": "tyhzt+zvxVIsuBTfbPIB+oD/jVQ=",
    "links": {
      "cdr": "http://1.facturaloperu-pro8.oo/downloads/document/cdr/05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8",
      "pdf": "http://1.facturaloperu-pro8.oo/downloads/document/pdf/05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8",
      "xml": "http://1.facturaloperu-pro8.oo/downloads/document/xml/05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8"
    },
    "total": 50,
    "number": "F001-63",
    "customer": {
      "name": "EMPRESA XYZ S.A.",
      "number": "10414711225",
      "identity_document_type_id": "6"
    },
    "filename": "20515809822-01-F001-63",
    "external_id": "05cbd9ba-d9ce-4a3a-8c78-7ea0ea8b5ab8",
    "date_of_issue": "2026-06-12",
    "state_type_id": "05",
    "sunat_response": {
      "code": "0",
      "description": "La Factura numero F001-63, ha sido aceptada"
    },
    "currency_type_id": "PEN",
    "document_type_id": "01",
    "number_to_letter": "Cincuenta  con 00/100 ",
    "exchange_rate_sale": "1.000",
    "state_type_description": "Aceptado"
  },
  "event": "document.accepted",
  "created_at": "2026-06-12T13:46:46-05:00",
  "api_version": "1.0"
}
```