export interface TitleCondition {
  countryId: string;
  searchNonOutput: number;
  searchClosed: number;
}

export interface TitleResultItem {
  id: number | null;
  countryId: string;
  holding: number;
  name: string;
  nameEnglish: string;
  sortOrder: number;
  winnerName: string | null;
  htmlFileName: string;
  htmlFileHolding: number | null;
  htmlFileModified: string;
  url: string | null;
  isClosed: boolean;
  isOutput: boolean;
  isOfficial: boolean;
  isTeam: boolean;
}

export interface Player {
  id: number;
  name: string;
  nameEnglish: string;
  nameOther: string;
  countryId: number;
  countryName: string;
  rankId: number;
  rankName: string;
  sex: string;
}
