export interface TitleCondition {
  countryId: string;
  searchNonOutput: number;
  searchClosed: number;
}

export interface TitleResponse {
  response: TitleResultItem[];
}

export interface TitleResultItem {
  id: number | null;
  countryId: string;
  holding: number;
  name: string;
  sortOrder: number;
  winnerName: string | null;
  htmlFileName: string;
  htmlFileHolding: number | null;
  htmlFileModified: string;
  url: string | null;
  isClosed: boolean;
  isOutput: boolean;
  isOfficial: boolean;
}

export interface Player {
  id: number;
  name: string;
  nameOther: string;
  countryId: number;
  rankId: number;
  rankName: string;
}
