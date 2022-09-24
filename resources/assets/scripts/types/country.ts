export interface CountryResponse {
  response: Country[];
}

export interface Country {
  id: number;
  code: string;
  name: string;
  name_english: string;
  has_title: boolean;
}
