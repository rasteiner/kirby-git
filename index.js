setTimeout(() => {
  if (window.commaship) {
    let shouldBeVisible = undefined

    function testVisibility(root) {
      if (shouldBeVisible === undefined) {
        shouldBeVisible = root.$api.get('rasteiner-git/auth').then((r) => r.result)
        setTimeout(() => {
          shouldBeVisible = undefined
        }, 0);
      }

      return shouldBeVisible;
    }

    commaship.register('Git', [
      {
        id: 'add-commit',
        label: 'Git Add and Commit',
        description: 'Stage content and create a new commit',
        filter: testVisibility,
        action: (root) => new commaship.Dialogue(async function*() {
          const message = yield new commaship.Question('Please enter a commit message')
          return root.$api.post('rasteiner-git/commits', { message }).then(r => r.result)
        }),
      },
      {
        id: 'log',
        label: 'Git Log',
        description: 'List all commits',
        filter: testVisibility,
        action: (root) => new commaship.Dialogue(async function*() {
          const hash = yield root.$api.get('rasteiner-git/commits').then(r => r.result)
          return root.$api.get(`rasteiner-git/commits/${hash}`).then(r => r.result)
        })
      },
      {
        id: 'rollback',
        label: 'Git Checkout',
        description: 'Go back to a specific commit (git revert --no-commit <hash>..HEAD)',
        filter: testVisibility,
        action: (root) => new commaship.Dialogue(async function* () {
          const hash = yield root.$api.get('rasteiner-git/commits').then(r => r.result)
          root.$api.get(`rasteiner-git/rollback/${hash}`).then(r => window.location.reload())
        })
      },
      {
        id: 'status',
        label: 'Git Status',
        description: 'Show git status',
        filter: testVisibility,
        action: (root) => root.$api.get(`rasteiner-git/status`).then(r => r.result)
      }
    ])
  }
}, 0);
