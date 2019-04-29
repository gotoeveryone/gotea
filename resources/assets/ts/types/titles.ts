export interface TitleCondition {
  countryId: string;
  searchType: number;
}

export interface TitleResultItem {
  id: number | null;
  countryId: string;
  holding: number;
  name: string;
  sortOrder: number;
  winnerName: string | null;
  htmlFileModified: string;
  url: string | null;
  isClosed: boolean;
}

export interface Player {
  id: number;
  name: string;
  nameOther: string;
  rankId: number;
  rankName: string;
}
