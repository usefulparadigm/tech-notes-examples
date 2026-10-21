# 워드프레스 MCP Adapter와 커스텀 서버 만들기

기술 노트 [워드프레스 MCP Adapter와 커스텀 서버 만들기](https://www.usefulparadigm.com/insights/)의 예제 코드입니다. <!-- 발행 후 글 주소로 교체 -->

워드프레스의 Abilities API로 어빌리티를 등록하고, [MCP Adapter](https://wordpress.org/plugins/mcp-adapter/)로 AI 클라이언트(Claude Code 등)에 노출합니다.

## 요구 사항

- 워드프레스 6.9 이상 (Abilities API 포함)
- MCP Adapter 플러그인 0.7.1 (2026년 10월 기준)
- PHP 7.4 이상
- 연결 테스트용: Node 22.19 이상 ([MCP Inspector](https://github.com/modelcontextprotocol/inspector)), 애플리케이션 비밀번호를 쓸 수 있는 HTTPS 사이트

## 파일

같은 코드를 두 가지 형태로 두었습니다. 편한 쪽 하나만 쓰세요.

```
code-snippets/                      Code Snippets 플러그인에 붙여 넣는 형태 (<?php 없음)
  01-list-recent-posts.php          기본 서버에 공개하는 어빌리티 1개
  02-custom-server.php              어빌리티 3개 + 커스텀 서버
mu-plugins/                         wp-content/mu-plugins/에 올리는 파일 형태
  up-mcp-01-list-recent-posts.php
  up-mcp-02-custom-server.php
```

01과 02는 같은 어빌리티 이름(`usefulparadigm/list-recent-posts`)과 카테고리를 등록하므로 동시에 켜지 마세요. 나중에 등록하는 쪽이 실패합니다.

## 01: 기본 서버에 공개하는 어빌리티

| 어빌리티 | 설명 | 권한 | 표시 |
| --- | --- | --- | --- |
| `usefulparadigm/list-recent-posts` | 최근 발행된 글의 제목, URL, 날짜 | `edit_posts` | readonly, `mcp.public: true` |

MCP Adapter 기본 서버(`/wp-json/mcp/mcp-adapter-default-server`)의 `mcp-adapter-discover-abilities` 목록에 나타납니다.

## 02: 커스텀 서버

| 어빌리티 | MCP 도구 이름 | 설명 | 권한 | 표시 |
| --- | --- | --- | --- | --- |
| `usefulparadigm/list-recent-posts` | `usefulparadigm-list-recent-posts` | 최근 글의 ID, 제목, URL, 날짜 | `edit_posts` | readonly |
| `usefulparadigm/create-draft` | `usefulparadigm-create-draft` | 새 글을 항상 초안으로 저장 | `edit_posts` | 비파괴 |
| `usefulparadigm/trash-own-draft` | `usefulparadigm-trash-own-draft` | 내가 만든 초안만 휴지통으로 | 초안 + 본인 작성 + `delete_post` | destructive |

- 엔드포인트: `https://사이트주소/wp-json/usefulparadigm/mcp`
- 세 어빌리티 모두 `mcp.public` 없이 커스텀 서버에만 노출됩니다(기본 서버의 discover 목록에는 나오지 않음).
- 기본 서버를 끄려면 파일 위쪽의 `mcp_adapter_create_default_server` 필터 줄의 주석을 푸세요.

## 사용법

### 1. 코드 넣기

- Code Snippets: 스니펫 → 새로 추가 → `code-snippets/` 파일 내용 붙여 넣기 → 실행 범위 "Run everywhere" → 저장 후 활성화
- mu-plugin: `mu-plugins/` 파일을 `wp-content/mu-plugins/`에 업로드

### 2. 에이전트 사용자와 애플리케이션 비밀번호

관리자 계정 대신 AI 전용 사용자를 만들고(예: `mcp-agent`, 역할은 글쓴이), 사용자 편집 화면에서 애플리케이션 비밀번호를 발급합니다.

### 3. MCP Inspector로 확인

```bash
npx @modelcontextprotocol/inspector
```

- Transport: STDIO
- Command: `npx`
- Arguments: `-y @automattic/mcp-wordpress-remote@latest`
- 환경변수
  - `WP_API_URL`: `https://사이트주소/wp-json/usefulparadigm/mcp` (기본 서버는 `/wp-json/mcp/mcp-adapter-default-server`)
  - `WP_API_USERNAME`: `mcp-agent`
  - `WP_API_PASSWORD`: 애플리케이션 비밀번호

### 4. Claude Code 연결

```bash
claude mcp add wp-site \
  --env WP_API_URL=https://사이트주소/wp-json/usefulparadigm/mcp \
  --env WP_API_USERNAME=mcp-agent \
  --env WP_API_PASSWORD='애플리케이션 비밀번호' \
  -- npx -y @automattic/mcp-wordpress-remote@latest
```

서버 이름(`wp-site`)은 `add` 바로 뒤에 씁니다. `--env` 뒤에 두면 이름을 환경변수로 읽어 오류가 납니다. 어디서나 쓰려면 `--scope user`를 붙이세요.

## 유의사항

- 운영 사이트라면 스테이징에서 먼저 확인하세요.
- 테스트가 끝나면 애플리케이션 비밀번호를 지우고, 쓰지 않는 코드는 비활성화하세요.
- 다른 플러그인이 등록한 어빌리티도 기본 서버에 공개될 수 있습니다. 기본 서버를 쓴다면 discover 결과를 주기적으로 확인하세요.
- MCP Adapter는 1.0 이전 버전이라 업데이트에 따라 `create_server()` 인자 등이 바뀔 수 있습니다.
