# Codex로 워드프레스 웹사이트 제작하기

기술 노트 [Codex로 워드프레스 웹사이트 제작하기](https://www.usefulparadigm.com/2026/11/09/wordpress-site-with-codex/)의 예제입니다.

Codex(ChatGPT Desktop)에 [WordPress Studio](https://developer.wordpress.com/studio/) MCP를 연결하고, 가상의 동네 로스터리 카페 요건으로 사이트(블록 테마와 콘텐츠)를 만들었습니다. 별도 스킬 패키지는 설치하지 않았습니다. Studio MCP가 워드프레스 작업 지침과 스킬을 함께 제공합니다.

## 파일

```
prompt.md                 Codex에 준 요건 원문 (가상 카페 '골목커피')
theme/                    Codex가 만든 블록 테마 (wp-content/themes/golmokcoffee)
content/                  DB에 저장된 콘텐츠의 블록 마크업 (참고용 스냅숏)
  pages/                  홈, 메뉴, 원두, 소개·오시는 길, 소식 페이지
  synced-patterns/        동기화 패턴: 영업시간과 주소, 전화번호, 대표 메뉴, 이번 주 원두, 나머지 메뉴
  navigation-16.html      상단 메뉴
  uploads/                Codex가 만들어 미디어에 올린 원두 이미지 3장
```

테마 파일에는 가게 정보가 거의 없습니다. 영업시간·주소·메뉴·원두는 Codex가 동기화 패턴(DB의 `wp_block`)으로 만들어 여러 페이지에서 불러 씁니다. 그래서 `theme/`만으로는 사이트가 재현되지 않아, DB의 해당 콘텐츠를 `content/`에 따로 남겼습니다.

커밋은 요청 단위로 나눴습니다. 처음 요청으로 만든 원본, 추가 요청 1(이미지), 추가 요청 2(푸터 전화번호) 순서입니다. 사람이 직접 고친 파일은 없습니다. `git log`와 `git diff`로 단계별 변화를 볼 수 있습니다.

## 실습 환경

| 항목 | 버전 |
| --- | --- |
| WordPress Studio | 2.1.1 |
| WordPress | 7.1.4 |
| Codex(ChatGPT Desktop) | GPT-6.1 Sol, 추론 Medium |
| MCP 연결 | `codex mcp add wordpress-studio -- studio mcp` (Studio CLI: `npm install -g wp-studio`) |
| 실습 날짜 | 2026년 11월 9일 |

## 점검 결과

| 점검 항목 | 결과 |
| --- | --- |
| theme.json 토큰화 (블록 속성에 색상값·px 직접 사용 여부) | 템플릿·파트·DB 콘텐츠 모두 0건. 색은 theme.json 팔레트 6색을 프리셋으로 사용 |
| 블록 유효성 (사이트 에디터 "Invalid block"/복구 경고) | 경고 없음 |
| 운영 정보 한 곳 관리 (전화번호·영업시간·메뉴) | 동기화 패턴 5개로 관리. 전화번호는 별도 패턴으로 분리해 푸터와 방문 안내에서 함께 사용 |
| 패턴 수정 | 사이트 에디터에서 패턴을 고쳐도 문제 없음 |
| 걸린 시간 | 처음 생성 4분 26초, 푸터 전화번호 추가 1분 |
| 사람 개입 | 처음 요청 뒤 추가 요청 2회(이미지, 푸터 전화번호). 직접 고친 파일 없음 |

토큰화 점검에 쓴 명령(`patterns/` 폴더는 이번 테마에 없음):

```bash
grep -rnE '#[0-9a-fA-F]{3,8}\b|[0-9]+px' theme/templates theme/parts theme/patterns
```

## 사용법

- 결과를 다시 만들어 보려면 Studio에 빈 사이트 `golmokcoffee`를 만들고, Codex에 Studio MCP를 연결한 뒤 `prompt.md`의 요청을 차례로 넣으세요. 모델 특성상 결과는 매번 조금씩 다릅니다.
- `theme/`만 쓰려면 사이트의 `wp-content/themes/golmokcoffee/`로 복사하고 관리자 화면의 외모 → 테마에서 활성화하세요. 가게 정보는 DB에 있으므로 테마만으로는 페이지 내용이 비어 있습니다.
- `content/`는 DB 콘텐츠를 살펴보기 위한 참고용입니다. 그대로 가져오려면 패턴을 새로 만들어 코드 편집기에 붙여 넣어야 하고, `wp:block`의 `ref` 번호와 이미지 주소(`http://localhost:8881/...`)를 새 사이트에 맞게 바꿔야 합니다.

## 유의사항

- 실습용 예제입니다. 운영 사이트에 그대로 쓰기보다 블록 테마 구조를 살펴보는 용도로 보세요.
- 가게 이름, 주소, 전화번호, 인스타그램 계정은 모두 가상입니다.
