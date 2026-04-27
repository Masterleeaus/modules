@extends('layouts/layoutMaster')

@section('title', 'Organization Hierarchy')

@section('content')
  <div class="container mt-4">
    <h2 class="text-center mb-4">Organization Hierarchy</h2>

    <div class="p-4">
      <div class="org-chart-container text-center">
        @if (!empty($hierarchy))
          <div class="org-chart">
            @foreach ($hierarchy as $node)
              @include('hrcore::organisation-hierarchy.partials.node', ['node' => $node])
            @endforeach
          </div>
        @else
          <p class="text-center">No hierarchy data available.</p>
        @endif
      </div>
    </div>
  </div>
@endsection
@section('page-script')
  <script>
    document.querySelectorAll('.org-node').forEach(node => {
      node.addEventListener('click', () => {
        const children = node.parentElement.querySelector('.org-children');
        if (children) {
          children.classList.toggle('d-none');
        }
      });
    });
  </script>

@endsection

@section('page-style')
  <style>
    /* Organization Chart Styles */
    .org-chart-container {
      overflow-x: auto;
    }

    .org-chart {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 20px;
    }

    .org-node {
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 16px;
      min-width: 200px;
      position: relative;
    }

    .org-node::after {
      content: '';
      position: absolute;
      width: 2px;
      height: 20px;
      background: #ccc;
      top: -20px;
      left: 50%;
      transform: translateX(-50%);
    }

    .org-node img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      margin-bottom: 8px;
    }

    .org-node h5 {
      font-size: 14px;
      font-weight: bold;
      margin-bottom: 4px;
    }

    .org-node p {
      font-size: 12px;
      color: #666;
      margin: 0;
    }

    .org-children {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
      padding-top: 20px;
      position: relative;
    }

    .org-children::before {
      content: '';
      position: absolute;
      top: 0;
      left: 10%;
      right: 10%;
      height: 2px;
      background: #ccc;
    }

    .org-children .org-node::after {
      top: -20px;
    }
  </style>
@endsection
