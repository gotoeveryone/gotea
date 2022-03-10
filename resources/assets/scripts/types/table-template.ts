export interface TableTemplateListResponse {
  response: TableTemplateList;
}

export interface TableTemplateList {
  total: number;
  items: TableTemplate[];
}

export interface TableTemplate {
  id: number;
  title: string;
  content: string;
  created: string;
  modified: string;
}
