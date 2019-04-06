export interface RankingCondition {
  year: string;
  country: string;
  limit: number;
  from: string;
  to: string;
}

export interface RankingResult {
  countryCode: string;
  countryName: string;
  year: number;
  lastUpdate: string;
  count: number;
  ranking: RankingResultItem[];
}

export interface RankingResultItem {
  id: number;
  rank: number;
  name: string;
  win: number;
  lose: number;
  draw: number;
  percentage: string;
  sex: string;
  url: string;
}
